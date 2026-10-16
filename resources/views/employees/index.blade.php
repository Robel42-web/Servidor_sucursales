<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados | Sucursales</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="flex min-h-screen">

        <!-- Barra lateral -->
        <aside class="bg-slate-950 p-6 text-white md:min-h-screen md:w-64 md:shrink-0">
            <div class="mb-10 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-xl">
                    B
                </div>
                <div>
                    <h1 class="text-lg font-bold">Sucursales</h1>
                    <p class="text-xs text-slate-400">Administración bancaria</p>
                </div>
            </div>

            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-slate-500">
                Menú principal
            </p>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">
                    Panel principal
                </a>
                <a href="{{ route('employees.index') }}"
                    class="block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white">
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
        <main class="min-w-0 flex-1 p-5 md:p-10">

            <!-- Encabezado -->
            <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-blue-600">
                        Administración de sucursal
                    </p>

                    <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">
                        Empleados
                    </h2>

                    <p class="mt-2 text-slate-500">
                        Consulta y administra el personal de la sucursal.
                    </p>
                </div>

                <a href="{{ route('employees.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white shadow-lg shadow-blue-600/20 transition-all duration-200 hover:-translate-y-0.5 hover:bg-blue-700 hover:shadow-xl">

                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                    </svg>

                    Registrar empleado
                </a>
            </header>

            <!-- Mensaje de éxito -->
            @if (session('success'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                    </svg>

                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Errores -->
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                    <p class="font-semibold">Hay errores en el formulario:</p>

                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Panel de empleados -->
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <!-- Buscador sin lupa -->
                <div class="border-b border-slate-100 p-5 md:p-6">
                    <form method="GET" action="{{ route('employees.index') }}" class="flex flex-col gap-3 sm:flex-row">

                        <div class="min-w-0 flex-1">
                            <input type="search" name="buscar" value="{{ $buscar }}"
                                placeholder="Buscar por nombre, número o correo..."
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <button type="submit"
                            class="rounded-xl bg-slate-800 px-5 py-3 font-semibold text-white transition-colors hover:bg-slate-700">
                            Buscar
                        </button>

                        @if ($buscar !== '')
                            <a href="{{ route('employees.index') }}"
                                class="rounded-xl border border-slate-300 px-5 py-3 text-center font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-slate-900">
                                Limpiar
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Título y total -->
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-5 md:px-6">
                    <div>
                        <h3 class="font-bold text-slate-900">
                            Personal registrado
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Administración y estado del personal.
                        </p>
                    </div>

                    <span
                        class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-700">
                        {{ $empleados->total() }} empleado(s)
                    </span>
                </div>

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px] text-left text-sm">

                        <thead
                            class="border-y border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-4 font-semibold">Número</th>
                                <th class="px-4 py-4 font-semibold">Nombre</th>
                                <th class="px-4 py-4 font-semibold">Puesto</th>
                                <th class="px-4 py-4 font-semibold">Correo</th>
                                <th class="px-4 py-4 font-semibold">Estado</th>
                                <th class="px-4 py-4 text-center font-semibold">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($empleados as $empleado)
                                <tr class="transition-colors hover:bg-blue-50/40">

                                    <td class="whitespace-nowrap px-4 py-4 font-semibold text-slate-700">
                                        {{ $empleado->employee_number }}
                                    </td>

                                    <td class="px-4 py-4 font-medium text-slate-900">
                                        {{ $empleado->full_name }}
                                    </td>

                                    <td class="px-4 py-4 text-slate-600">
                                        {{ $empleado->position?->name ?? 'Sin puesto' }}
                                    </td>

                                    <td class="px-4 py-4 text-slate-600">
                                        {{ $empleado->email }}
                                    </td>

                                    <!-- Estado -->
                                    <td class="px-4 py-4">
                                        @php
                                            $colores = [
                                                'active' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
                                                'inactive' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
                                                'suspended' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
                                            ];

                                            $estados = [
                                                'active' => 'Activo',
                                                'inactive' => 'Inactivo',
                                                'suspended' => 'Suspendido',
                                            ];
                                        @endphp

                                        <span
                                            class="inline-flex items-center gap-2 whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $colores[$empleado->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20' }}">

                                            <span class="h-2 w-2 shrink-0 rounded-full
                                                        @if ($empleado->status === 'active')
                                                            bg-emerald-500
                                                        @elseif ($empleado->status === 'inactive')
                                                            bg-slate-400
                                                        @elseif ($empleado->status === 'suspended')
                                                            bg-amber-500
                                                        @else
                                                            bg-slate-400
                                                        @endif">
                                            </span>

                                            {{ $estados[$empleado->status] ?? $empleado->status }}
                                        </span>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-3">

                                            <!-- Editar -->
                                            <a href="{{ route('employees.edit', $empleado) }}" title="Editar empleado"
                                                aria-label="Editar empleado"
                                                class="group inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition-all duration-200 hover:scale-110 hover:bg-blue-600 hover:text-white hover:shadow-md hover:shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">

                                                <svg class="h-5 w-5 transition-transform duration-200 group-hover:rotate-[-8deg]"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"
                                                    aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </a>

                                            <!-- Dar de baja -->
                                            @if ($empleado->status !== 'inactive')
                                                <form method="POST" action="{{ route('employees.deactivate', $empleado) }}"
                                                    onsubmit="return confirm('¿Deseas dar de baja a este empleado?');"
                                                    class="inline-flex shrink-0">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit" title="Dar de baja al empleado"
                                                        aria-label="Dar de baja al empleado"
                                                        class="group inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 transition-all duration-200 hover:scale-110 hover:bg-rose-600 hover:text-white hover:shadow-md hover:shadow-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2">

                                                        <svg class="h-5 w-5 transition-transform duration-200 group-hover:scale-110"
                                                            fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                                            stroke="currentColor" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M18 7H6m4-3h4m-9 3 .8 12.1A2 2 0 0 0 7.8 21h8.4a2 2 0 0 0 2-1.9L19 7M10 11v6m4-6v6" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-14 text-center">
                                        <div class="flex flex-col items-center gap-3 text-slate-500">

                                            <div
                                                class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">
                                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16 18a4 4 0 0 0-8 0m12 3H4v-1a8 8 0 0 1 16 0v1ZM12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                                                </svg>
                                            </div>

                                            <p class="font-medium">
                                                No se encontraron empleados.
                                            </p>

                                            @if ($buscar !== '')
                                                <a href="{{ route('employees.index') }}" class="text-blue-600 hover:underline">
                                                    Limpiar búsqueda
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="border-t border-slate-100 px-5 py-5 md:px-6">
                    {{ $empleados->links() }}
                </div>
            </section>




        </main>
    </div>
</body>

</html>