@extends('layouts.branch-shell')

@section('pageTitle', 'Operaciones bancarias | Sucursales')
@section('breadcrumb', 'Operaciones bancarias')
@section('heading', 'Operaciones bancarias')
@section('pageDescription', 'Realiza depósitos y retiros autorizados, y consulta el historial de movimientos.')
@section('activeMenu', 'operations')

@section('content')
@if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $errorMessage)<li>{{ $errorMessage }}</li>@endforeach</ul></div>
    @endif
    @if ($error)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">{{ $error }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold">Buscar cuenta</h2>
        <form method="GET" action="{{ route('bank.operations.index') }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
            <input name="numero_cuenta" value="{{ $numero }}" required maxlength="30" class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" placeholder="Número de cuenta">
            <button class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Consultar saldo e historial</button>
        </form>
    </section>

    @if ($cuenta)
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm text-slate-500">Cuenta {{ $cuenta['numero_cuenta'] ?? $numero }}</p>
                    <h2 class="mt-1 text-xl font-bold">{{ $cuenta['titular'] ?? 'Titular sin nombre' }}</h2>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ ($cuenta['estado'] ?? '') === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ ($cuenta['estado'] ?? '') === 'active' ? 'Activa' : ($cuenta['estado'] ?? 'Estado desconocido') }}</span>
            </div>
            <p class="mt-4 text-sm text-slate-500">Saldo registrado en el Banco Central</p>
            <p class="text-3xl font-bold">${{ number_format((float) ($cuenta['saldo'] ?? 0), 2) }} <span class="text-base font-medium text-slate-500">MXN</span></p>
        </section>

        @if (($cuenta['estado'] ?? '') === 'active')
            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-emerald-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-emerald-800">Depositar efectivo</h2>
                    <p class="mt-1 text-sm text-slate-500">El Banco Central validará la cuenta y actualizará el saldo y el efectivo de la sucursal de forma atómica.</p>
                    <form method="POST" action="{{ route('bank.operations.deposit') }}" class="mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="numero_cuenta" value="{{ $cuenta['numero_cuenta'] ?? $numero }}">
                        <input type="hidden" name="idempotency_key_deposito" value="{{ $depositKey }}">
                        <div><label class="mb-1 block text-sm font-medium" for="monto_deposito">Importe (MXN)</label><input id="monto_deposito" name="monto_deposito" type="number" min="0.01" max="999999999999.99" step="0.01" required value="{{ old('monto_deposito') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
                        <div><label class="mb-1 block text-sm font-medium" for="descripcion_deposito">Descripción (opcional)</label><input id="descripcion_deposito" name="descripcion_deposito" maxlength="255" value="{{ old('descripcion_deposito') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Depósito en efectivo"></div>
                        <button onclick="return confirm('¿Confirmas el depósito? Se enviará al Banco Central.');" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Confirmar depósito</button>
                    </form>
                </section>

                <section class="rounded-2xl border border-amber-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-amber-800">Retirar efectivo</h2>
                    <p class="mt-1 text-sm text-slate-500">Se requiere saldo suficiente en la cuenta y efectivo disponible en la sucursal.</p>
                    <form method="POST" action="{{ route('bank.operations.withdraw') }}" class="mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="numero_cuenta" value="{{ $cuenta['numero_cuenta'] ?? $numero }}">
                        <input type="hidden" name="idempotency_key_retiro" value="{{ $withdrawKey }}">
                        <div><label class="mb-1 block text-sm font-medium" for="monto_retiro">Importe (MXN)</label><input id="monto_retiro" name="monto_retiro" type="number" min="0.01" max="999999999999.99" step="0.01" required value="{{ old('monto_retiro') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
                        <div><label class="mb-1 block text-sm font-medium" for="descripcion_retiro">Descripción (opcional)</label><input id="descripcion_retiro" name="descripcion_retiro" maxlength="255" value="{{ old('descripcion_retiro') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Retiro en efectivo"></div>
                        <button onclick="return confirm('¿Confirmas el retiro? El Banco Central comprobará el saldo de la cuenta y el efectivo de la sucursal.');" class="rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">Confirmar retiro</button>
                    </form>
                </section>
            </div>
        @else
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">La cuenta no está activa; no se pueden ejecutar depósitos ni retiros.</div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold">Historial de transacciones</h2>
                <p class="mt-1 text-sm text-slate-500">Movimientos consultados directamente al Banco Central; se muestran los registros de la página actual.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3">Fecha</th><th class="px-4 py-3">Código</th><th class="px-4 py-3">Tipo</th><th class="px-4 py-3">Importe</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3">Descripción</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse ($movimientos as $movimiento)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3">{{ !empty($movimiento['created_at']) ? \Illuminate\Support\Carbon::parse($movimiento['created_at'])->format('d/m/Y H:i') : '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs">{{ $movimiento['transaction_code'] ?? $movimiento['id'] ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ match ($movimiento['type'] ?? '') { 'deposit' => 'Depósito', 'withdrawal' => 'Retiro', default => $movimiento['type'] ?? '—' } }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-semibold">${{ number_format((float) ($movimiento['amount'] ?? 0), 2) }} MXN</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ ($movimiento['status'] ?? '') === 'completed' ? 'Completada' : ($movimiento['status'] ?? '—') }}</td>
                            <td class="px-4 py-3">{{ $movimiento['description'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Esta cuenta todavía no tiene movimientos.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
    <p class="text-xs text-slate-500">Los saldos, validaciones de efectivo e historial son responsabilidad del Banco Central. Si una petición pierde conexión, revisa el historial antes de repetirla.</p>
@endsection
