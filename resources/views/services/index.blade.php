<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atención al cliente | Sucursales</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<div class="flex min-h-screen">
    <aside class="bg-slate-950 p-6 text-white md:min-h-screen md:w-64 md:shrink-0">
        <div class="mb-10 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-xl font-bold">
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
            <a href="{{ route('dashboard') }}"
               class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">
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
               class="block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white">
                Atención al cliente
            </a>

            <a href="{{ url('/solicitudes') }}"
               class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">
                Solicitudes
            </a>
        </nav>

        <div class="mt-10 rounded-xl border border-slate-800 p-4">
            <p class="text-sm font-semibold">Sistema de sucursal</p>
            <p class="mt-1 text-xs text-slate-400">
                Datos conectados a Supabase mediante Laravel.
            </p>
        </div>
    </aside>

    <main class="min-w-0 flex-1 p-5 sm:p-8">
        <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-600">Administración de sucursal</p>
                <h2 class="mt-1 text-3xl font-bold text-slate-900">Atención al cliente</h2>
                <p class="mt-2 text-slate-500">
                    Consulta y administra las atenciones registradas.
                </p>
            </div>

            <a href="{{ route('services.create') }}"
               class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
                + Registrar atención
            </a>
        </header>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form action="{{ route('services.index') }}" method="GET"
                  class="flex flex-col gap-3 sm:flex-row">
                <input
                    type="text"
                    name="buscar"
                    value="{{ $buscar ?? request('buscar') }}"
                    placeholder="Buscar por tipo, descripción o empleado..."
                    class="min-w-0 flex-1 rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >

                <button type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white hover:bg-slate-700">
                    Buscar
                </button>

                <a href="{{ route('services.index') }}"
                   class="rounded-xl border border-slate-300 px-5 py-3 text-center font-semibold hover:bg-slate-50">
                    Limpiar
                </a>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="font-bold text-slate-900">Atenciones registradas</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Listado de servicios de atención al cliente.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Empleado</th>
                            <th class="px-6 py-4 font-semibold">Tipo de atención</th>
                            <th class="px-6 py-4 font-semibold">Descripción</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold">Fecha</th>
                            <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($atenciones as $atencion)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">
                                    {{ $atencion->employee?->full_name ?? 'Sin empleado asignado' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $atencion->service_type }}
                                </td>

                                <td class="max-w-xs px-6 py-4 text-slate-600">
                                    <p class="line-clamp-2">
                                        {{ $atencion->description }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($atencion->status === 'open')
                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            Abierta
                                        </span>
                                    @elseif ($atencion->status === 'in_progress')
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                            En proceso
                                        </span>
                                    @elseif ($atencion->status === 'closed')
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            Cerrada
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                            {{ $atencion->status }}
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-slate-600">
                                    {{ $atencion->attended_at
                                        ? \Illuminate\Support\Carbon::parse($atencion->attended_at)->format('d/m/Y H:i')
                                        : 'Sin fecha' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a href="{{ route('services.edit', $atencion) }}"
                                       class="inline-flex rounded-lg border border-blue-200 px-3 py-2 font-semibold text-blue-700 hover:bg-blue-50">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="font-semibold text-slate-700">No hay atenciones registradas</p>
                                    <p class="mt-1 text-slate-500">
                                        Registra una atención nueva o cambia los criterios de búsqueda.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>
