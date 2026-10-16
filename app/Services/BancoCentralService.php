<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class BancoCentralService
{
    /**
     * Las operaciones bancarias usan la API Key de la sucursal autenticada,
     * no el token general configurado para el Banco Central.
     */
    private function cliente(): PendingRequest
    {
        $codigoSucursal = session('branch.codigo');

        if (!is_string($codigoSucursal) || trim($codigoSucursal) === '') {
            throw new RuntimeException('No se pudo identificar la sucursal autenticada.');
        }

        $credencial = DB::table('branch_api_credentials')
            ->where('branch_code', $codigoSucursal)
            ->first();

        if (!$credencial || !is_string($credencial->api_key_encrypted ?? null)) {
            throw new RuntimeException('La sucursal no tiene una API Key vinculada.');
        }

        try {
            $apiKey = Crypt::decryptString($credencial->api_key_encrypted);
        } catch (DecryptException $e) {
            throw new RuntimeException(
                'No se pudo recuperar la API Key cifrada de la sucursal.',
                0,
                $e
            );
        }

        return Http::baseUrl(rtrim((string) config('services.banco_central.url'), '/'))
            ->withToken($apiKey)
            ->acceptJson()
            ->timeout(15);
    }

    public function salud()
    {
        return Http::baseUrl(rtrim((string) config('services.banco_central.url'), '/'))
            ->acceptJson()
            ->timeout(15)
            ->get('/api/salud');
    }

    public function crearCuenta(string $titular, string $numeroCuenta)
    {
        return $this->cliente()->post('/api/cuentas', [
            'titular' => $titular,
            'numero_cuenta' => $numeroCuenta,
        ]);
    }

    public function buscarCuentaPorNumero(string $numeroCuenta)
    {
        return $this->cliente()->get(
            '/api/cuentas/numero/' . rawurlencode($numeroCuenta)
        );
    }

    public function consultarCuenta(string $id)
    {
        return $this->cliente()->get('/api/cuentas/' . rawurlencode($id));
    }

    public function consultarSaldo(string $id)
    {
        return $this->cliente()->get('/api/cuentas/' . rawurlencode($id) . '/saldo');
    }

    public function consultarMovimientos(string $id)
    {
        return $this->cliente()->get('/api/cuentas/' . rawurlencode($id) . '/movimientos');
    }

    public function depositar(
        string $cuentaId,
        int $montoCentavos,
        string $idempotencyKey,
        string $descripcion
    ) {
        return $this->cliente()->post('/api/operaciones/depositos', [
            'cuenta_id' => $cuentaId,
            'monto_centavos' => $montoCentavos,
            'idempotency_key' => $idempotencyKey,
            'descripcion' => $descripcion,
        ]);
    }

    public function retirar(
        string $cuentaId,
        int $montoCentavos,
        string $idempotencyKey,
        string $descripcion
    ) {
        return $this->cliente()->post('/api/operaciones/retiros', [
            'cuenta_id' => $cuentaId,
            'monto_centavos' => $montoCentavos,
            'idempotency_key' => $idempotencyKey,
            'descripcion' => $descripcion,
        ]);
    }

    public function validarAccesoSucursal(string $codigoSucursal, string $apiKey)
    {
        return Http::baseUrl(rtrim((string) config('services.banco_central.url'), '/'))
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->post('/api/sucursales/validar-acceso', [
                'branch_code' => $codigoSucursal,
            ]);
    }

    public function autenticarSucursal(string $email, string $password)
    {
        return Http::baseUrl(rtrim((string) config('services.banco_central.url'), '/'))
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->post('/api/sucursales/login', [
                'email' => $email,
                'password' => $password,
            ]);
    }
}
