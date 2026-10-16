<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales | Panel administrativo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="min-h-screen md:flex">

        <!-- Menú lateral -->
        <aside class="bg-slate-950 p-6 text-white md:min-h-screen md:w-64 md:shrink-0">
            <div class="mb-10 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-xl">
                    B
                </div>
                <div>
                    <h1 class="text-lg font-bold">Sucursales (Has sido hackeado)</h1>
                    <p class="text-xs text-slate-400">Administración bancaria</p>
                </div>
            </div>

            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-slate-500">
                Menú principal
            </p>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white">
                    Panel principal
                </a>

                <a href="{{ route('employees.index') }}"
                    class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">
                    Empleados
                </a>

                <a href="{{ route('shifts.index') }}"
                    class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">
                    Turnos
                </a>

                <a href="{{ route('services.index') }}"
                    class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">
                    Atención al cliente
                </a>

                <a href="{{ url('/solicitudes') }}"
                    class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">
                    Solicitudes
                </a>
<a href="{{ route('bank.accounts.index') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Cuentas bancarias</a>
<a href="{{ route('bank.operations.index') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Operaciones bancarias</a>
            </nav>


            <div class="mt-10 rounded-xl border border-slate-800 bg-slate-900 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Sucursal activa
                </p>

                <p class="mt-2 text-base font-bold text-white">
                    {{ session('branch.nombre', 'Sucursal no identificada') }}
                </p>

                <p class="mt-1 text-sm text-blue-400">
                    Código: {{ session('branch.codigo', 'Sin código') }}
                </p>

                <div class="mt-4 border-t border-slate-700 pt-3">
                    <p class="text-xs text-slate-400">Usuario conectado</p>
                    <p class="mt-1 text-sm font-medium text-white">
                        {{ session('branch_user.nombre', 'Usuario') }}
                    </p>
                    <p class="mt-1 break-all text-xs text-slate-400">
                        {{ session('branch_user.correo', '') }}
                    </p>
                </div>

                @if (session('branch.id') === 'local-dev')
                    <div class="mt-4 rounded-lg bg-amber-500/10 px-3 py-2">
                        <p class="text-xs font-semibold text-amber-400">
                            Modo de desarrollo local
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf

                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl
               border border-rose-500/30 bg-rose-500/10 px-4 py-3
               font-semibold text-rose-300 transition
               hover:bg-rose-600 hover:text-white
               focus:outline-none focus:ring-2 focus:ring-rose-400">

                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                            aria-hidden="true">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M18 15l3-3m0 0-3-3m3 3H9" />
                        </svg>

                        Cerrar sesión
                    </button>
                </form>

            </div>

        </aside>

        <!-- Contenido principal -->
        <main class="min-w-0 flex-1 p-5 md:p-8">
            <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Administración / Inicio</p>
                    <h2 class="mt-1 text-3xl font-bold text-slate-900">
                        Panel principal
                    </h2>
                    <p class="mt-2 text-slate-500">
                        Resumen de las operaciones de la sucursal.
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                    <p class="text-xs text-slate-500">Fecha de consulta</p>
                    <p class="font-semibold">{{ now()->format('d/m/Y') }}</p>
                </div>
            </header>

            <!-- Tarjetas de indicadores -->
            <section class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">Total de empleados</p>
                    <p class="mt-3 text-3xl font-bold">{{ $totalEmpleados }}</p>
                    <p class="mt-2 text-sm text-slate-500">Personal registrado</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">Empleados activos</p>
                    <p class="mt-3 text-3xl font-bold text-emerald-600">
                        {{ $empleadosActivos }}
                    </p>
                    <p class="mt-2 text-sm text-slate-500">Con estado activo</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">Turnos asignados</p>
                    <p class="mt-3 text-3xl font-bold">{{ $totalTurnos }}</p>
                    <p class="mt-2 text-sm text-slate-500">Registros de turnos</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">Atenciones abiertas</p>
                    <p class="mt-3 text-3xl font-bold text-amber-600">
                        {{ $atencionesAbiertas }}
                    </p>
                    <p class="mt-2 text-sm text-slate-500">Pendientes de concluir</p>
                </article>
            </section>

            <!-- Tabla de empleados -->
            <section id="empleados" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-5">
                    <div>
                        <h3 class="text-lg font-bold">Empleados recientes</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Últimos registros consultados desde la base de datos.
                        </p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">
                        {{ $totalEmpleados }} registrados
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-5 py-4">Número</th>
                                <th class="px-5 py-4">Empleado</th>
                                <th class="px-5 py-4">Puesto</th>
                                <th class="px-5 py-4">Correo</th>
                                <th class="px-5 py-4">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($ultimosEmpleados as $empleado)
                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-5 py-4 font-medium">
                                        {{ $empleado->employee_number }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        {{ $empleado->full_name }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        {{ $empleado->position?->name ?? 'Sin puesto' }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-500">
                                        {{ $empleado->email }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        @if ($empleado->status === 'active')
                                            <span
                                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                Activo
                                            </span>
                                        @elseif ($empleado->status === 'inactive')
                                            <span
                                                class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                                                Inactivo
                                            </span>
                                        @else
                                            <span
                                                class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                Suspendido
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                                        No hay empleados registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Resumen de solicitudes -->
            <section id="solicitudes" class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-bold">Solicitudes pendientes</h3>
                <p class="mt-2 text-3xl font-bold">{{ $solicitudesPendientes }}</p>
                <p class="mt-1 text-sm text-slate-500">
                    Solicitudes que todavía requieren resolución.
                </p>
            </section>

            <footer class="mt-8 text-center text-xs text-slate-400">
                Sistema de administración de sucursales · Laravel
            </footer>
        </main>
    </div>
</body>

</html>