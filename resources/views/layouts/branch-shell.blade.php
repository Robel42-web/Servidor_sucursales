<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('pageTitle', 'Sucursales | Panel administrativo')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    @php($activeMenu = trim($__env->yieldContent('activeMenu')))
    <div class="min-h-screen md:flex">
        <aside class="bg-slate-950 p-6 text-white md:min-h-screen md:w-64 md:shrink-0">
            <div class="mb-10 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-xl">B</div>
                <div>
                    <h1 class="text-lg font-bold">Sucursales</h1>
                    <p class="text-xs text-slate-400">Administración bancaria</p>
                </div>
            </div>

            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-slate-500">Menú principal</p>
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="{{ $activeMenu === 'dashboard' ? 'block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white' : 'block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800' }}">Panel principal</a>
                <a href="{{ route('employees.index') }}" class="{{ $activeMenu === 'employees' ? 'block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white' : 'block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800' }}">Empleados</a>
                <a href="{{ route('shifts.index') }}" class="{{ $activeMenu === 'shifts' ? 'block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white' : 'block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800' }}">Turnos</a>
                <a href="{{ route('services.index') }}" class="{{ $activeMenu === 'services' ? 'block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white' : 'block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800' }}">Atención al cliente</a>
                <a href="{{ route('requests.index') }}" class="{{ $activeMenu === 'requests' ? 'block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white' : 'block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800' }}">Solicitudes</a>
                <a href="{{ route('bank.accounts.index') }}" class="{{ $activeMenu === 'accounts' ? 'block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white' : 'block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800' }}">Cuentas bancarias</a>
                <a href="{{ route('bank.operations.index') }}" class="{{ $activeMenu === 'operations' ? 'block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white' : 'block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800' }}">Operaciones bancarias</a>
            </nav>

            <div class="mt-10 rounded-xl border border-slate-800 bg-slate-900 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sucursal activa</p>
                <p class="mt-2 break-words text-base font-bold text-white">{{ session('branch.nombre', 'Sucursal no identificada') }}</p>
                <p class="mt-1 text-sm text-blue-400">Código: {{ session('branch.codigo', 'Sin código') }}</p>
                <div class="mt-4 border-t border-slate-700 pt-3">
                    <p class="text-xs text-slate-400">Usuario conectado</p>
                    <p class="mt-1 text-sm font-medium text-white">{{ session('branch_user.nombre', 'Usuario') }}</p>
                    <p class="mt-1 break-all text-xs text-slate-400">{{ session('branch_user.correo', '') }}</p>
                </div>
                @if (session('branch.id') === 'local-dev')
                    <div class="mt-4 rounded-lg bg-amber-500/10 px-3 py-2">
                        <p class="text-xs font-semibold text-amber-400">Modo de desarrollo local</p>
                    </div>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 font-semibold text-rose-300 transition hover:bg-rose-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-rose-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M18 15l3-3m0 0-3-3m3 3H9" />
                        </svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        <main class="min-w-0 flex-1 p-5 md:p-8">
            <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Administración / @yield('breadcrumb', 'Banco')</p>
                    <h2 class="mt-1 text-3xl font-bold text-slate-900">@yield('heading', 'Administración bancaria')</h2>
                    <p class="mt-2 text-slate-500">@yield('pageDescription', '')</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                    <p class="text-xs text-slate-500">Fecha de consulta</p>
                    <p class="font-semibold">{{ now()->format('d/m/Y') }}</p>
                </div>
            </header>

            @yield('content')

            <footer class="mt-8 text-center text-xs text-slate-400">Sistema de administración de sucursales · Laravel</footer>
        </main>
    </div>
</body>
</html>
