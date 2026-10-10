<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales | Solicitudes</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<div class="min-h-screen md:flex">

    <aside class="bg-slate-950 p-6 text-white md:min-h-screen md:w-64 md:shrink-0">
        <div class="mb-10 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-xl font-bold">B</div>
            <div>
                <h1 class="text-lg font-bold">Sucursales</h1>
                <p class="text-xs text-slate-400">Administración bancaria</p>
            </div>
        </div>

        <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-slate-500">
            Menú principal
        </p>

        <nav class="space-y-2">
            <a href="{{ route('dashboard') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Panel principal</a>
            <a href="{{ route('employees.index') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Empleados</a>
            <a href="{{ route('shifts.index') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Turnos</a>
            <a href="{{ route('services.index') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Atención al cliente</a>
            <a href="{{ route('requests.index') }}" class="block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white">Solicitudes</a>
        </nav>

        <div class="mt-10 rounded-xl border border-slate-800 p-4">
            <p class="text-sm font-semibold">Sistema de sucursal</p>
            <p class="mt-1 text-xs text-slate-400">Datos conectados a Supabase mediante Laravel.</p>
        </div>
    </aside>

    <main class="min-w-0 flex-1 p-5 md:p-8">
        <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">Administración / Solicitudes</p>
                <h2 class="mt-1 text-3xl font-bold text-slate-900">Solicitudes</h2>
                <p class="mt-2 text-slate-500">Consulta y administra las solicitudes de la sucursal.</p>
            </div>

            <a href="{{ route('requests.create') }}"
               class="rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
                + Nueva solicitud
            </a>
        </header>

        @if (session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                <p class="font-semibold">No se pudo completar la operación:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Total de solicitudes</p>
                <p class="mt-3 text-3xl font-bold">{{ $totalSolicitudes }}</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Pendientes</p>
                <p class="mt-3 text-3xl font-bold text-amber-600">{{ $pendientes }}</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Completadas</p>
                <p class="mt-3 text-3xl font-bold text-emerald-600">{{ $completadas }}</p>
            </article>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <form method="GET" action="{{ route('requests.index') }}"
                  class="flex flex-wrap gap-3 border-b border-slate-200 p-5">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar por tipo o descripción..."
                       class="min-w-0 flex-1 rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">

                <select name="status"
                        class="rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                    <option value="">Todos los estados</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pendiente</option>
                    <option value="approved" @selected(request('status') === 'approved')>Aprobada</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rechazada</option>
                    <option value="completed" @selected(request('status') === 'completed')>Completada</option>
                </select>

                <button class="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white hover:bg-slate-700">
                    Buscar
                </button>

                <a href="{{ route('requests.index') }}"
                   class="rounded-xl border border-slate-300 px-5 py-3 hover:bg-slate-50">
                    Limpiar
                </a>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Tipo</th>
                            <th class="px-5 py-4">Empleado</th>
                            <th class="px-5 py-4">Atención relacionada</th>
                            <th class="px-5 py-4">Fecha</th>
                            <th class="px-5 py-4">Estado</th>
                            <th class="px-5 py-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($solicitudes as $solicitud)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-semibold">
                                    {{ $solicitud->request_type }}
                                    <p class="mt-1 max-w-xs truncate font-normal text-slate-500">
                                        {{ $solicitud->description ?: 'Sin descripción' }}
                                    </p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    {{ $solicitud->employee?->full_name ?? 'Sin asignar' }}
                                </td>
                                <td class="px-5 py-4">
                                    {{ $solicitud->customerService?->service_type ?? 'Sin relación' }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-500">
                                    {{ $solicitud->created_at?->format('d/m/Y H:i') ?? 'Sin fecha' }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    @php
                                        $estado = [
                                            'pending' => ['Pendiente', 'bg-amber-100 text-amber-700'],
                                            'approved' => ['Aprobada', 'bg-blue-100 text-blue-700'],
                                            'rejected' => ['Rechazada', 'bg-red-100 text-red-700'],
                                            'completed' => ['Completada', 'bg-emerald-100 text-emerald-700'],
                                        ][$solicitud->status] ?? [$solicitud->status, 'bg-slate-100 text-slate-700'];
                                    @endphp
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $estado[1] }}">
                                        {{ $estado[0] }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <a href="{{ route('requests.edit', $solicitud->id) }}"
                                       class="font-semibold text-blue-600 hover:text-blue-800">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                                    No hay solicitudes que coincidan con la búsqueda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 p-5">
                {{ $solicitudes->links() }}
            </div>
        </section>

        <footer class="mt-8 text-center text-xs text-slate-400">
            Sistema de administración de sucursales · Laravel
        </footer>
    </main>
</div>
</body>
</html>
