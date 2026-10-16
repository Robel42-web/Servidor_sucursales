@extends('layouts.branch-shell')

@section('pageTitle', 'Cuentas bancarias | Sucursales')
@section('breadcrumb', 'Cuentas bancarias')
@section('heading', 'Cuentas bancarias')
@section('pageDescription', 'Registra cuentas de clientes y consulta su información directamente en el Banco Central.')
@section('activeMenu', 'accounts')

@section('content')
@if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
            <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $errorMessage)<li>{{ $errorMessage }}</li>@endforeach</ul>
        </div>
    @endif
    @if ($error)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">{{ $error }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Dar de alta una cuenta</h2>
            <p class="mt-1 text-sm text-slate-500">El Banco Central generará la cuenta y su saldo inicial será de $0.00 MXN.</p>
            <form method="POST" action="{{ route('bank.accounts.store') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="titular" class="mb-1 block text-sm font-medium">Nombre completo del titular</label>
                    <input id="titular" name="titular" value="{{ old('titular') }}" required maxlength="150" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    @error('titular')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <button class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Crear cuenta</button>
            </form>
            <p class="mt-4 text-xs text-slate-500">El número de cuenta se genera y verifica antes del alta. No introduzcas información bancaria sensible en el nombre del titular.</p>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Consultar una cuenta</h2>
            <p class="mt-1 text-sm text-slate-500">Busca por número para ver su titular, saldo y estado directamente en el Banco Central.</p>
            <form method="GET" action="{{ route('bank.accounts.index') }}" class="mt-5 space-y-4">
                <div>
                    <label for="numero_cuenta" class="mb-1 block text-sm font-medium">Número de cuenta</label>
                    <input id="numero_cuenta" name="numero_cuenta" value="{{ $numero }}" required maxlength="30" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" placeholder="Ej. 992142470">
                </div>
                <button class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Buscar cuenta</button>
            </form>
        </section>
    </div>

    @if ($cuenta)
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                <div>
                    <p class="text-sm font-medium text-slate-500">Cuenta encontrada</p>
                    <h2 class="mt-1 text-xl font-bold">{{ $cuenta['titular'] ?? 'Titular sin nombre' }}</h2>
                    <p class="mt-1 text-sm text-slate-600">Número: <span class="font-semibold">{{ $cuenta['numero_cuenta'] ?? $numero }}</span></p>
                </div>
                <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ ($cuenta['estado'] ?? '') === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ ($cuenta['estado'] ?? '') === 'active' ? 'Activa' : ($cuenta['estado'] ?? 'Estado desconocido') }}</span>
            </div>
            <div class="mt-5 grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-sm text-slate-500">Saldo disponible</p><p class="mt-1 text-2xl font-bold">${{ number_format((float) ($cuenta['saldo'] ?? 0), 2) }} <span class="text-sm font-medium text-slate-500">MXN</span></p></div>
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-sm text-slate-500">Moneda</p><p class="mt-1 text-xl font-semibold">{{ $cuenta['moneda'] ?? 'MXN' }}</p></div>
                <div class="flex items-center"><a class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700" href="{{ route('bank.operations.index', ['numero_cuenta' => $cuenta['numero_cuenta'] ?? $numero]) }}">Operar y ver historial</a></div>
            </div>
        </section>
    @endif
    <p class="text-xs text-slate-500">Los datos de cuenta y saldo se consultan en el Banco Central. Las operaciones y su historial no se almacenan como saldos locales en Laravel.</p>
@endsection
