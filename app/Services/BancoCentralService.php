<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class BancoCentralService
{
    private function cliente(): PendingRequest
    {
        return Http::baseUrl(
            rtrim(config('services.banco_central.url'), '/')
        )
            ->withToken(config('services.banco_central.token'))
            ->acceptJson()
            ->timeout(15);
    }

    public function salud()
    {
        return Http::baseUrl(
            rtrim(config('services.banco_central.url'), '/')
        )
            ->acceptJson()
            ->timeout(15)
            ->get('/api/salud');
    }

    public function buscarCuentaPorNumero(string $numeroCuenta)
    {
        return $this->cliente()->get(
            '/api/cuentas/numero/' . rawurlencode($numeroCuenta)
        );
    }

    public function consultarCuenta(string $id)
    {
        return $this->cliente()->get(
            '/api/cuentas/' . rawurlencode($id)
        );
    }

    public function consultarSaldo(string $id)
    {
        return $this->cliente()->get(
            '/api/cuentas/' . rawurlencode($id) . '/saldo'
        );
    }

    public function consultarMovimientos(string $id)
    {
        return $this->cliente()->get(
            '/api/cuentas/' . rawurlencode($id) . '/movimientos'
        );
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
    public function validarAccesoSucursal(
        string $codigoSucursal,
        string $apiKey
    ) {
        return Http::baseUrl(
            rtrim(config('services.banco_central.url'), '/')
        )
            ->acceptJson()
            ->timeout(15)
            ->post('/api/sucursales/validar-acceso', [
                'branch_code' => $codigoSucursal,
                'api_key' => $apiKey,
            ]);
    }

    public function autenticarSucursal(string $email, string $password)
{
    return Http::baseUrl(
        rtrim(config('services.banco_central.url'), '/')
    )
        ->acceptJson()
        ->asJson()
        ->timeout(15)
        ->post('/api/sucursales/login', [
            'email' => $email,
            'password' => $password,
        ]);
}
}
