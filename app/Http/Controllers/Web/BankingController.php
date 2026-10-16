<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BancoCentralService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class BankingController extends Controller
{
    public function accounts(Request $request, BancoCentralService $central)
    {
        $numero = trim((string) $request->query('numero_cuenta', ''));
        $cuenta = null;
        $error = null;

        if ($numero !== '') {
            validator(['numero_cuenta' => $numero], [
                'numero_cuenta' => ['required', 'string', 'max:30'],
            ])->validate();

            try {
                $respuesta = $central->buscarCuentaPorNumero($numero);
                if ($respuesta->successful()) {
                    $cuenta = $respuesta->json('cuenta');
                    if (!is_array($cuenta) || empty($cuenta['id'])) {
                        $cuenta = null;
                        $error = 'El Banco Central no devolvió una cuenta con identificador válido.';
                    }
                } elseif ($respuesta->status() === 404) {
                    $error = 'No se encontró una cuenta con ese número.';
                } else {
                    $error = $this->mensajeErrorCentral($respuesta->json(), $respuesta->status());
                }
            } catch (ConnectionException $e) {
                report($e);
                $error = 'No fue posible conectar con el Banco Central. No se modificó ninguna cuenta.';
            } catch (Throwable $e) {
                report($e);
                $error = 'No fue posible consultar la cuenta. Verifica la configuración de la sucursal.';
            }
        }

        return view('banking.accounts', compact('numero', 'cuenta', 'error'));
    }

    public function storeAccount(Request $request, BancoCentralService $central): RedirectResponse
    {
        $datos = $request->validate([
            'titular' => ['required', 'string', 'max:150'],
        ]);

        try {
            // La API central requiere numero_cuenta. Generamos uno de 10 dígitos
            // y comprobamos que no esté ocupado antes de intentar crearlo.
            for ($intento = 0; $intento < 8; $intento++) {
                $numero = (string) random_int(1_000_000_000, 9_999_999_999);
                $consulta = $central->buscarCuentaPorNumero($numero);

                if ($consulta->successful()) {
                    continue;
                }

                if ($consulta->status() !== 404) {
                    return back()->withInput()->withErrors([
                        'cuenta' => $this->mensajeErrorCentral($consulta->json(), $consulta->status()),
                    ]);
                }

                $creacion = $central->crearCuenta($datos['titular'], $numero);

                if ($creacion->status() === 422) {
                    // Una petición simultánea pudo ocupar ese número; probamos otro.
                    continue;
                }

                if (!$creacion->successful()) {
                    return back()->withInput()->withErrors([
                        'cuenta' => $this->mensajeErrorCentral($creacion->json(), $creacion->status()),
                    ]);
                }

                // Algunas versiones del endpoint de alta devuelven id=null;
                // la consulta por número recupera el UUID real ya persistido.
                $verificacion = $central->buscarCuentaPorNumero($numero);
                if (!$verificacion->successful() || empty($verificacion->json('cuenta.id'))) {
                    return redirect()->route('bank.accounts.index')->withErrors([
                        'cuenta' => 'El Banco Central confirmó el alta, pero no pudimos recuperar el UUID. Busca la cuenta por el número: ' . $numero,
                    ]);
                }

                return redirect()->route('bank.accounts.index', ['numero_cuenta' => $numero])
                    ->with('success', 'Cuenta creada correctamente. Número de cuenta: ' . $numero . '. Saldo inicial: $0.00 MXN.');
            }
        } catch (ConnectionException $e) {
            report($e);
            return back()->withInput()->withErrors([
                'cuenta' => 'Se perdió la conexión con el Banco Central. No vuelvas a enviar el alta sin comprobar primero si la cuenta se creó.',
            ]);
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->withErrors([
                'cuenta' => 'No fue posible completar el alta de la cuenta. Revisa el registro de Laravel.',
            ]);
        }

        return back()->withInput()->withErrors([
            'cuenta' => 'No se pudo encontrar un número de cuenta disponible. Inténtalo nuevamente.',
        ]);
    }

    public function operations(Request $request, BancoCentralService $central)
    {
        $numero = trim((string) $request->query('numero_cuenta', ''));
        $cuenta = null;
        $movimientos = [];
        $error = null;

        if ($numero !== '') {
            validator(['numero_cuenta' => $numero], [
                'numero_cuenta' => ['required', 'string', 'max:30'],
            ])->validate();

            try {
                $respuesta = $central->buscarCuentaPorNumero($numero);
                if (!$respuesta->successful()) {
                    $error = $respuesta->status() === 404
                        ? 'No se encontró una cuenta con ese número.'
                        : $this->mensajeErrorCentral($respuesta->json(), $respuesta->status());
                } else {
                    $cuenta = $respuesta->json('cuenta');
                    if (!is_array($cuenta) || empty($cuenta['id'])) {
                        $cuenta = null;
                        $error = 'La respuesta del Banco Central no incluye un UUID válido para la cuenta.';
                    } else {
                        $historial = $central->consultarMovimientos((string) $cuenta['id']);
                        if ($historial->successful()) {
                            $movimientos = $historial->json('data') ?? [];
                        } else {
                            $error = 'La cuenta se consultó, pero no se pudo cargar su historial: '
                                . $this->mensajeErrorCentral($historial->json(), $historial->status());
                        }
                    }
                }
            } catch (ConnectionException $e) {
                report($e);
                $error = 'No fue posible conectar con el Banco Central.';
            } catch (Throwable $e) {
                report($e);
                $error = 'No fue posible consultar la cuenta o su historial.';
            }
        }

        return view('banking.operations', [
            'numero' => $numero,
            'cuenta' => $cuenta,
            'movimientos' => is_array($movimientos) ? $movimientos : [],
            'error' => $error,
            'depositKey' => old('idempotency_key_deposito') ?: (string) Str::uuid(),
            'withdrawKey' => old('idempotency_key_retiro') ?: (string) Str::uuid(),
        ]);
    }

    public function deposit(Request $request, BancoCentralService $central): RedirectResponse
    {
        return $this->performOperation($request, $central, 'deposit');
    }

    public function withdraw(Request $request, BancoCentralService $central): RedirectResponse
    {
        return $this->performOperation($request, $central, 'withdrawal');
    }

    private function performOperation(
        Request $request,
        BancoCentralService $central,
        string $type
    ): RedirectResponse {
        $deposit = $type === 'deposit';
        $amountField = $deposit ? 'monto_deposito' : 'monto_retiro';
        $descriptionField = $deposit ? 'descripcion_deposito' : 'descripcion_retiro';
        $idempotencyField = $deposit ? 'idempotency_key_deposito' : 'idempotency_key_retiro';

        $datos = $request->validate([
            'numero_cuenta' => ['required', 'string', 'max:30'],
            $amountField => ['required', 'string', 'regex:/^\d{1,12}(?:\.\d{1,2})?$/'],
            $descriptionField => ['nullable', 'string', 'max:255'],
            $idempotencyField => ['required', 'string', 'max:100'],
        ]);

        $numero = $datos['numero_cuenta'];
        $importe = $datos[$amountField];
        [$pesos, $centavos] = array_pad(explode('.', $importe, 2), 2, '');
        $montoCentavos = ((int) $pesos * 100) + (int) str_pad($centavos, 2, '0');

        if ($montoCentavos < 1 || $montoCentavos > 99_999_999_999_999) {
            throw ValidationException::withMessages([
                $amountField => 'El importe debe ser mayor que cero y estar dentro del límite permitido.',
            ]);
        }

        try {
            $lookup = $central->buscarCuentaPorNumero($numero);
            if (!$lookup->successful()) {
                $mensaje = $lookup->status() === 404
                    ? 'No se encontró la cuenta indicada.'
                    : $this->mensajeErrorCentral($lookup->json(), $lookup->status());
                return $this->operationError($numero, $mensaje, $datos);
            }

            $cuenta = $lookup->json('cuenta');
            if (!is_array($cuenta) || empty($cuenta['id'])) {
                return $this->operationError(
                    $numero,
                    'El Banco Central no devolvió el UUID de la cuenta.',
                    $datos
                );
            }

            if (($cuenta['estado'] ?? 'active') !== 'active') {
                return $this->operationError($numero, 'La cuenta no está activa.', $datos);
            }

            $descripcion = trim((string) ($datos[$descriptionField] ?? ''));
            if ($descripcion === '') {
                $descripcion = $deposit ? 'Depósito en sucursal' : 'Retiro en sucursal';
            }

            $respuesta = $deposit
                ? $central->depositar(
                    (string) $cuenta['id'],
                    $montoCentavos,
                    $datos[$idempotencyField],
                    $descripcion
                )
                : $central->retirar(
                    (string) $cuenta['id'],
                    $montoCentavos,
                    $datos[$idempotencyField],
                    $descripcion
                );

            if (!$respuesta->successful()) {
                $mensaje = $this->mensajeErrorCentral($respuesta->json(), $respuesta->status());
                return $this->operationError($numero, $mensaje, $datos);
            }

            $json = $respuesta->json() ?? [];
            $mensaje = $json['mensaje'] ?? ($deposit ? 'Depósito completado.' : 'Retiro completado.');
            if (isset($json['saldo'])) {
                $mensaje .= ' Saldo actual de la cuenta: $' . $json['saldo'] . ' MXN.';
            }
            if (($json['repetida'] ?? false) === true) {
                $mensaje .= ' Se devolvió el resultado de una operación ya registrada; no se duplicó.';
            }

            return redirect()->route('bank.operations.index', ['numero_cuenta' => $numero])
                ->with('success', $mensaje);
        } catch (ConnectionException $e) {
            report($e);
            return $this->operationError(
                $numero,
                'No se pudo confirmar la respuesta del Banco Central. Revisa el historial antes de volver a intentar; se conservará la misma clave de idempotencia.',
                $datos
            );
        } catch (Throwable $e) {
            report($e);
            return $this->operationError(
                $numero,
                'No fue posible completar la operación. Revisa el registro de Laravel antes de repetirla.',
                $datos
            );
        }
    }

    private function operationError(string $numero, string $mensaje, array $datos): RedirectResponse
    {
        return redirect()->route('bank.operations.index', ['numero_cuenta' => $numero])
            ->withErrors(['operacion' => $mensaje])
            ->withInput($datos);
    }

    private function mensajeErrorCentral(?array $json, int $status): string
    {
        $mensaje = $json['mensaje'] ?? $json['message'] ?? null;
        if (is_string($mensaje) && trim($mensaje) !== '') {
            return $mensaje;
        }
        return 'El Banco Central respondió HTTP ' . $status . '.';
    }
}
