<?php

namespace App\Http\Middleware;

use App\Services\BancoCentralService;
use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchAuthenticated
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (session('branch_authenticated') !== true) {
            return redirect()->route('login');
        }

        // Siempre permitir cerrar sesión, aunque el Banco Central no responda.
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        $branch = $request->session()->get('branch', []);
        $codigo = is_array($branch) ? ($branch['codigo'] ?? null) : null;

        if (!is_string($codigo) || trim($codigo) === '') {
            return $this->cerrarSesion(
                $request,
                'La sesión no tiene una sucursal válida. Inicia sesión nuevamente.'
            );
        }

        $credencial = DB::table('branch_api_credentials')
            ->where('branch_code', $codigo)
            ->first();

        if (!$credencial) {
            return $this->cerrarSesion(
                $request,
                'No existe una API Key registrada para esta sucursal.'
            );
        }

        try {
            $apiKey = Crypt::decryptString(
                $credencial->api_key_encrypted
            );
        } catch (DecryptException $e) {
            report($e);

            return $this->cerrarSesion(
                $request,
                'No se pudo recuperar la credencial de la sucursal. Debe vincularse nuevamente.'
            );
        }

        try {
            $respuesta = app(BancoCentralService::class)
                ->validarAccesoSucursal($codigo, $apiKey);
        } catch (ConnectionException $e) {
            report($e);

            return response(
                'No fue posible verificar la autorización con el Banco Central. Intenta nuevamente.',
                503
            );
        }

        if (in_array($respuesta->status(), [401, 403], true)) {
            return $this->cerrarSesion(
                $request,
                'El Banco Central ya no autoriza el acceso de esta sucursal.'
            );
        }

        // Ante errores del servidor central, no permitir continuar.
        if (!$respuesta->successful()) {
            return response(
                'No fue posible verificar la autorización con el Banco Central. Intenta nuevamente.',
                503
            );
        }

        $datos = $respuesta->json();
        $sucursal = is_array($datos)
            ? ($datos['sucursal'] ?? [])
            : [];

        $codigoCentral = is_array($sucursal)
            ? strtoupper(trim((string) ($sucursal['codigo'] ?? '')))
            : '';

        $estadoCentral = is_array($sucursal)
            ? strtolower(trim((string) ($sucursal['estado'] ?? '')))
            : '';

        if (
            !is_array($datos)
            || ($datos['valida'] ?? false) !== true
            || $codigoCentral !== strtoupper(trim($codigo))
            || $estadoCentral !== 'active'
        ) {
            return $this->cerrarSesion(
                $request,
                'La sucursal no está activa o no coincide con la credencial registrada.'
            );
        }

        return $next($request);
    }

    private function cerrarSesion(
        Request $request,
        string $mensaje
    ): Response {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'login' => $mensaje,
        ]);
    }
}
