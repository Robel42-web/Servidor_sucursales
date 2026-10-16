<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BancoCentralService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;

class BranchLoginController extends Controller
{
    public function showLogin()
    {
        if (session('branch_authenticated') === true) {
            return redirect()->route('dashboard');
        }

        return view('auth.branch-login');
    }


    public function login(
        \Illuminate\Http\Request $request,
        \App\Services\BancoCentralService $bancoCentral
    ) {
        $datos = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);
        // Acceso de desarrollo: solo funciona en entorno local.
        if (
            app()->environment('local')
            && config('services.branch.local_login_enabled') === true
            && hash_equals(
                (string) config('services.branch.local_login_email'),
                $datos['email']
            )
            && hash_equals(
                (string) config('services.branch.local_login_password'),
                $datos['password']
            )
        ) {
            $request->session()->regenerate();

            $request->session()->put([
                'branch_authenticated' => true,

                'branch' => [
                    'id' => 'local-dev',
                    'codigo' => 'DEV-001',
                    'nombre' => 'Sucursal de Desarrollo',
                ],

                'branch_user' => [
                    'nombre' => 'Administrador de Desarrollo',
                    'correo' => $datos['email'],
                    'rol' => 'branch_admin',
                ],
            ]);

            return redirect()->intended(route('dashboard'));
        }
        try {
            $respuesta = $bancoCentral->autenticarSucursal(
                $datos['email'],
                $datos['password']
            );
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()
                ->onlyInput('email')
                ->withErrors([
                    'login' => 'No fue posible conectar con el Banco Central.',
                ]);
        }

        if ($respuesta->status() === 404 || $respuesta->status() === 405) {
            return back()
                ->onlyInput('email')
                ->withErrors([
                    'login' => 'El servicio de autenticación de sucursales aún no está disponible.',
                ]);
        }

        if (!$respuesta->successful()) {
            return back()
                ->onlyInput('email')
                ->withErrors([
                    'login' => 'Credenciales incorrectas o acceso rechazado.',
                ]);
        }

        $resultado = $respuesta->json();
        $sucursal = $resultado['sucursal'] ?? null;

        if (
            !is_array($resultado) ||
            ($resultado['success'] ?? false) !== true ||
            !is_array($sucursal) ||
            ($sucursal['estado'] ?? null) !== 'active'
        ) {
            return back()
                ->onlyInput('email')
                ->withErrors([
                    'login' => 'El Banco Central no autorizó el acceso.',
                ]);
        }

        $request->session()->regenerate();

        $request->session()->put([
            'branch_authenticated' => true,
            'branch' => [
                'id' => $sucursal['id'] ?? null,
                'codigo' => $sucursal['codigo'] ?? null,
                'nombre' => $sucursal['nombre'] ?? 'Sucursal',
            ],
        ]);

        return redirect()->intended(route('dashboard'));
    }


    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}