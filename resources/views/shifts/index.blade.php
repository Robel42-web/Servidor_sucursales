<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos | Sucursales</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="min-h-screen md:flex">

        {{-- MENÚ LATERAL --}}
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
                    class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">
                    Empleados
                </a>
                <a href="{{ route('shifts.index') }}"
                    class="block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white">
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

        {{-- CONTENIDO --}}
        <main class="min-w-0 flex-1 p-5 md:p-8">
            <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Administración / Turnos</p>
                    <h2 class="mt-1 text-3xl font-bold text-slate-900">Gestión de turnos</h2>
                    <p class="mt-2 text-slate-500">
                        Consulta y administra las asignaciones de horarios del personal.
                    </p>
                </div>
                <a href="{{ route('shifts.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white shadow-sm hover:bg-blue-700">
                    <span class="text-xl">+</span> Asignar turno
                </a>
            </header>

            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- HORARIOS --}}
            <section class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-xl">☀️</div>
                        <div>
                            <h3 class="font-bold text-slate-900">Turno matutino</h3>
                            <p class="text-sm text-slate-500">Horario de mañana</p>
                        </div>
                    </div>
                    <p class="mt-5 text-3xl font-bold text-amber-600">08:00 – 14:00</p>
                    <p class="mt-2 text-sm text-slate-500">Duración: 6 horas</p>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-xl">🌙
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900">Turno vespertino</h3>
                            <p class="text-sm text-slate-500">Horario de tarde</p>
                        </div>
                    </div>
                    <p class="mt-5 text-3xl font-bold text-indigo-600">14:00 – 20:00</p>
                    <p class="mt-2 text-sm text-slate-500">Duración: 6 horas</p>
                </article>
            </section>

            {{-- LISTADO DE TURNOS --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5">
                    <h3 class="text-lg font-bold text-slate-900">Asignaciones registradas</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Consulta empleados, horarios, fechas y estados.
                    </p>
                </div>

                <form method="GET" action="{{ route('shifts.index') }}" class="flex flex-col gap-3 p-5 sm:flex-row">
                    <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por empleado o número..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-800 placeholder:text-slate-500 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">

                    <button type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white hover:bg-slate-700">
                        Buscar
                    </button>

                    @if($buscar)
                        <a href="{{ route('shifts.index') }}"
                            class="rounded-xl border border-slate-300 px-5 py-3 text-center font-semibold text-slate-700 hover:bg-slate-100">
                            Limpiar
                        </a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Empleado</th>
                                <th class="px-6 py-4">Horario</th>
                                <th class="px-6 py-4">Fecha</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($turnos as $turno)
                                @php
                                    $estados = [
                                        'scheduled' => ['Programado', 'bg-blue-50 text-blue-700 ring-blue-600/20'],
                                        'completed' => ['Completado', 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
                                        'absent' => ['Ausente', 'bg-rose-50 text-rose-700 ring-rose-600/20'],
                                    ];
                                    $estado = $estados[$turno->status] ?? [$turno->status, 'bg-slate-100 text-slate-700 ring-slate-500/20'];
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-900">
                                            {{ $turno->employee->full_name ?? 'Sin empleado' }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $turno->employee->employee_number ?? '' }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-800">
                                            {{ $turno->shift->name ?? 'Sin horario' }}
                                        </p>
                                        @if($turno->shift)
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ substr((string) $turno->shift->start_time, 0, 5) }}
                                                – {{ substr((string) $turno->shift->end_time, 0, 5) }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-600">
                                        {{ $turno->work_date ? \Illuminate\Support\Carbon::parse($turno->work_date)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $estado[1] }}">
                                            {{ $estado[0] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('shifts.edit', $turno) }}"
                                            class="inline-flex rounded-xl bg-blue-50 px-4 py-2 font-semibold text-blue-700 hover:bg-blue-600 hover:text-white">
                                            Editar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        No hay turnos registrados.
                                        <a href="{{ route('shifts.create') }}"
                                            class="font-semibold text-blue-700 hover:underline">
                                            Asignar primer turno
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 p-4">
                    {{ $turnos->links() }}
                </div>
            </section>
        </main>
    </div>
</body>

</html>