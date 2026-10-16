<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BancoCentralService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Encryption\DecryptException;

class BranchLoginController extends Controller
{
    public function showLogin()
    {
        if (session('branch_authenticated') === true) {
            return redirect()->route('dashboard');
        }

        return view('auth.branch-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        // Evita reutilizar datos pendientes de un intento anterior.
        $request->session()->forget('pending_branch_login');

        $url = rtrim((string) config('services.supabase.url'), '/');
        // Admite los dos nombres usados durante la configuración del proyecto.
        $publishableKey = (string) (
            config('services.supabase.key')
            ?: config('services.supabase.publishable_key')
        );

        if ($url === '' || $publishableKey === '') {
            return back()->onlyInput('email')->withErrors([
                'login' => 'La conexión con Supabase no está configurada.',
            ]);
        }

        // 1. Autenticar con el mismo endpoint de Supabase que funciona en curl.
        try {
            $respuesta = Http::acceptJson()
                ->asJson()
                ->withHeaders(['apikey' => $publishableKey])
                ->timeout(10)
                ->post($url . '/auth/v1/token?grant_type=password', [
                    'email' => $datos['email'],
                    'password' => $datos['password'],
                ]);
        } catch (ConnectionException $e) {
            report($e);
            return back()->onlyInput('email')->withErrors([
                'login' => 'No fue posible conectar con Supabase.',
            ]);
        }

        if (!$respuesta->successful()) {
            return back()->onlyInput('email')->withErrors([
                'login' => 'Correo o contraseña incorrectos.',
            ]);
        }

        $usuarioAuth = $respuesta->json('user');
        $metadata = is_array($usuarioAuth) ? ($usuarioAuth['user_metadata'] ?? []) : [];
        $codigo = is_array($metadata) && is_string($metadata['branch_code'] ?? null)
            ? strtoupper(trim($metadata['branch_code']))
            : '';

        if (!is_array($usuarioAuth) || empty($usuarioAuth['id']) || $codigo === '') {
            return back()->onlyInput('email')->withErrors([
                'login' => 'Supabase no devolvió el UUID o el código de sucursal del usuario.',
            ]);
        }

        // Guardamos solo los datos mínimos del usuario mientras valida la API Key.
        // No guardamos la contraseña ni el access_token de Supabase.
        // Rotar la sesión después de autenticar correctamente en Supabase.
        $request->session()->regenerate();

        $pending = [
            'auth_user_id' => (string) $usuarioAuth['id'],
            'email' => (string) ($usuarioAuth['email'] ?? $datos['email']),
            'nombre' => is_array($metadata) && is_string($metadata['full_name'] ?? null)
                ? $metadata['full_name']
                : $datos['email'],
            'codigo' => $codigo,
            // Este flujo es para administradores de sucursal; no confiamos en un rol
            // editable desde user_metadata para conceder privilegios locales.
            'rol' => 'branch_admin',
        ];
        $request->session()->put('pending_branch_login', $pending);

        // 2. Si nunca se vinculó una clave para esta sucursal, pedirla una sola vez.
        $credencial = DB::table('branch_api_credentials')
            ->where('branch_code', $codigo)
            ->first();

        if (!$credencial) {
            return redirect()->route('branch.key.form');
        }

        try {
            $apiKey = Crypt::decryptString($credencial->api_key_encrypted);
        } catch (DecryptException $e) {
            report($e);
            return redirect()->route('branch.key.form')->withErrors([
                'api_key' => 'La clave almacenada no se puede recuperar. Introduce nuevamente la API Key completa de esta sucursal.',
            ]);
        }

        // 3. La clave almacenada se revalida en cada inicio de sesión.
        return $this->validarClaveYCompletar($request, app(BancoCentralService::class), $pending, $apiKey, true);
    }

    public function showBranchKeyForm(Request $request)
    {
        $pending = $request->session()->get('pending_branch_login');
        if (!is_array($pending) || empty($pending['auth_user_id']) || empty($pending['codigo'])) {
            return redirect()->route('login')->withErrors([
                'login' => 'Inicia sesión primero para identificar tu sucursal.',
            ]);
        }

        return view('auth.branch-api-key', [
            'branchCode' => $pending['codigo'],
            'userEmail' => $pending['email'] ?? '',
        ]);
    }

    public function submitBranchKey(Request $request, BancoCentralService $bancoCentral): RedirectResponse
    {
        $pending = $request->session()->get('pending_branch_login');
        if (!is_array($pending) || empty($pending['auth_user_id']) || empty($pending['codigo'])) {
            return redirect()->route('login')->withErrors([
                'login' => 'La sesión de validación expiró. Inicia sesión nuevamente.',
            ]);
        }

        $datos = $request->validate([
            'api_key' => ['required', 'string', 'min:20', 'max:2000'],
        ]);

        return $this->validarClaveYCompletar(
            $request,
            $bancoCentral,
            $pending,
            trim($datos['api_key'])
        );
    }

    private function validarClaveYCompletar(
        Request $request,
        BancoCentralService $bancoCentral,
        array $pending,
        string $apiKey,
        bool $claveGuardada = false
    ): RedirectResponse {
        try {
            $respuesta = $bancoCentral->validarAccesoSucursal($pending['codigo'], $apiKey);
        } catch (ConnectionException $e) {
            report($e);
            return redirect()->route('branch.key.form')->withErrors([
                'api_key' => 'No fue posible conectar con el Banco Central. La clave no se guardó; inténtalo nuevamente.',
            ]);
        }

        if ($respuesta->status() === 401) {
            if ($claveGuardada) {
                $request->session()->forget('pending_branch_login');

                return redirect()->route('login')->withErrors([
                    'login' => 'El Banco Central rechazó la API Key guardada. La clave pudo ser revocada o la sucursal ya no está autorizada.',
                ]);
            }

            return redirect()->route('branch.key.form')->withErrors([
                'api_key' => 'El Banco Central rechazó la API Key. Verifica que la clave sea completa y corresponda a esta sucursal.',
            ]);
        }

        if ($respuesta->status() === 403) {
            $request->session()->forget('pending_branch_login');
            return redirect()->route('login')->withErrors([
                'login' => 'El Banco Central no autoriza esta sucursal; puede estar inactiva.',
            ]);
        }

        if (!$respuesta->successful()) {
            return redirect()->route('branch.key.form')->withErrors([
                'api_key' => 'El Banco Central respondió HTTP ' . $respuesta->status() . '. No se guardó la clave ni se creó el perfil.',
            ]);
        }

        $resultado = $respuesta->json();
        $sucursal = is_array($resultado) ? ($resultado['sucursal'] ?? []) : [];
        $codigoCentral = is_array($sucursal) && is_string($sucursal['codigo'] ?? null)
            ? strtoupper(trim($sucursal['codigo']))
            : '';
        $estadoCentral = is_array($sucursal) && is_string($sucursal['estado'] ?? null)
            ? strtolower(trim($sucursal['estado']))
            : '';

        if (!is_array($resultado) || ($resultado['valida'] ?? false) !== true) {
            return redirect()->route('branch.key.form')->withErrors([
                'api_key' => 'El Banco Central no confirmó la validez de la API Key.',
            ]);
        }

        // El endpoint identifica la sucursal a partir del Bearer token.
        // No se permite usar una clave válida de otra sucursal.
        if ($codigoCentral !== $pending['codigo']) {
            return redirect()->route('branch.key.form')->withErrors([
                'api_key' => 'La API Key corresponde a ' . ($codigoCentral ?: 'otra sucursal')
                    . ', pero esta cuenta pertenece a ' . $pending['codigo'] . '.',
            ]);
        }

        if ($estadoCentral !== 'active') {
            $request->session()->forget('pending_branch_login');
            return redirect()->route('login')->withErrors([
                'login' => 'La sucursal ' . $pending['codigo'] . ' no está activa en el Banco Central.',
            ]);
        }

        // 4. Solo después de comprobar que la sucursal está activa se revisa/crea el perfil.
        $perfil = DB::table('user_profiles')
            ->where('auth_user_id', $pending['auth_user_id'])
            ->first();

        if ($perfil && ($perfil->status !== 'active' || empty($perfil->role))) {
            $request->session()->forget('pending_branch_login');
            return redirect()->route('login')->withErrors([
                'login' => 'El perfil local existe, pero está inactivo o no tiene un rol asignado. Contacta al administrador.',
            ]);
        }

        $rolNuevo = trim((string) ($pending['rol'] ?? ''));
        if ($rolNuevo === '') {
            $rolNuevo = 'branch_admin';
        }
        $nombreSucursal = is_string($sucursal['nombre'] ?? null) && $sucursal['nombre'] !== ''
            ? $sucursal['nombre']
            : 'Sucursal ' . $pending['codigo'];
        $idSucursal = $sucursal['id'] ?? $pending['codigo'];
        $cifrada = Crypt::encryptString($apiKey);
        $ahora = now();

        DB::transaction(function () use ($pending, $cifrada, $ahora, $rolNuevo) {
            $credencialExistente = DB::table('branch_api_credentials')
                ->where('branch_code', $pending['codigo'])
                ->first();

            $campos = [
                'api_key_encrypted' => $cifrada,
                'last_validated_at' => $ahora,
                'updated_at' => $ahora,
            ];

            if ($credencialExistente) {
                DB::table('branch_api_credentials')
                    ->where('branch_code', $pending['codigo'])
                    ->update($campos);
            } else {
                DB::table('branch_api_credentials')->insert($campos + [
                    'branch_code' => $pending['codigo'],
                    'created_at' => $ahora,
                ]);
            }

            $perfilExistente = DB::table('user_profiles')
                ->where('auth_user_id', $pending['auth_user_id'])
                ->first();

            if (!$perfilExistente) {
                DB::table('user_profiles')->insert([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'auth_user_id' => $pending['auth_user_id'],
                    'employee_id' => null,
                    'role' => $rolNuevo,
                    'status' => 'active',
                    'must_change_password' => false,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }
        });

        $perfil = DB::table('user_profiles')
            ->where('auth_user_id', $pending['auth_user_id'])
            ->first();
        $request->session()->regenerate();
        $request->session()->forget('pending_branch_login');
        $request->session()->put([
            'branch_authenticated' => true,
            'branch' => [
                'id' => $idSucursal,
                'codigo' => $pending['codigo'],
                'nombre' => $nombreSucursal,
            ],
            'branch_user' => [
                'id' => $pending['auth_user_id'],
                'profile_id' => $perfil->id,
                'nombre' => $pending['nombre'] ?? $pending['email'],
                'correo' => $pending['email'],
                'rol' => $perfil->role,
            ],
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
