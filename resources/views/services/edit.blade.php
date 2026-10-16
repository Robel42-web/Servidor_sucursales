<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar atención | Sucursales</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<div class="flex min-h-screen">
    <aside class="bg-slate-950 p-6 text-white md:min-h-screen md:w-64 md:shrink-0">
        <div class="mb-10 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-xl font-bold">B</div>
            <div>
                <h1 class="text-lg font-bold">Sucursales</h1>
                <p class="text-xs text-slate-400">Administración bancaria</p>
            </div>
        </div>

        <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-slate-500">Menú principal</p>

        <nav class="space-y-2">
            <a href="{{ route('dashboard') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Panel principal</a>
            <a href="{{ route('employees.index') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Empleados</a>
            <a href="{{ route('shifts.index') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Turnos</a>
            <a href="{{ route('services.index') }}" class="block rounded-xl bg-blue-600 px-4 py-3 font-medium text-white">Atención al cliente</a>
            <a href="{{ url('/solicitudes') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Solicitudes</a>
<a href="{{ route('bank.accounts.index') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Cuentas bancarias</a>
<a href="{{ route('bank.operations.index') }}" class="block rounded-xl px-4 py-3 text-slate-300 hover:bg-slate-800">Operaciones bancarias</a>
        </nav>

        <div class="mt-10 rounded-xl border border-slate-800 p-4">
            <p class="text-sm font-semibold">Sistema de sucursal</p>
            <p class="mt-1 text-xs text-slate-400">Datos conectados a Supabase mediante Laravel.</p>
        </div>
    </aside>

    <main class="min-w-0 flex-1 p-5 sm:p-8">
        <header class="mb-8">
            <p class="text-sm font-medium text-blue-600">Administración de sucursal</p>
            <h2 class="mt-1 text-3xl font-bold text-slate-900">Editar atención</h2>
            <p class="mt-2 text-slate-500">Modifica los datos de la atención registrada.</p>
        </header>

        @if ($errors->any())
            <div class="mb-6 max-w-3xl rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('services.update', $record) }}"
              class="max-w-3xl space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @method('PUT')

            <div>
                <label for="employee_id" class="mb-2 block text-sm font-semibold">Empleado responsable</label>
                <select id="employee_id" name="employee_id" required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">Selecciona un empleado</option>
                    @foreach ($empleados as $empleado)
                        <option value="{{ $empleado->id }}" @selected(old('employee_id', $record->employee_id) == $empleado->id)>
                            {{ $empleado->full_name }} — {{ $empleado->employee_number }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="service_type" class="mb-2 block text-sm font-semibold">Tipo de atención</label>
                <input id="service_type" name="service_type" type="text" maxlength="150"
                       value="{{ old('service_type', $record->service_type) }}" required
                       class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label for="description" class="mb-2 block text-sm font-semibold">Descripción</label>
                <textarea id="description" name="description" rows="4" maxlength="2000" required
                          class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('description', $record->description) }}</textarea>
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-semibold">Estado</label>
                <select id="status" name="status" required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="open" @selected(old('status', $record->status) === 'open')>Abierta</option>
                    <option value="in_progress" @selected(old('status', $record->status) === 'in_progress')>En proceso</option>
                    <option value="closed" @selected(old('status', $record->status) === 'closed')>Cerrada</option>
                </select>
            </div>

            <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                <p>
                    <strong>Fecha de atención:</strong>
                    {{ $record->attended_at ? \Illuminate\Support\Carbon::parse($record->attended_at)->format('d/m/Y H:i') : 'Sin fecha' }}
                </p>
                <p class="mt-1">
                    <strong>Fecha de cierre:</strong>
                    {{ $record->closed_at ? \Illuminate\Support\Carbon::parse($record->closed_at)->format('d/m/Y H:i') : 'Aún sin cerrar' }}
                </p>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('services.index') }}"
                   class="rounded-xl border border-slate-300 px-6 py-3 text-center font-semibold hover:bg-slate-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                    Guardar cambios
                </button>
            </div>
        </form>
    </main>
</div>
</body>
</html>
