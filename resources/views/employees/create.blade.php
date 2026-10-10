<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar empleado | Sucursales</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 p-5 text-slate-800 md:p-10">
    <main class="mx-auto max-w-3xl">
        <a href="{{ route('employees.index') }}"
           class="text-sm font-medium text-blue-600 hover:underline">
            ← Volver a empleados
        </a>

        <header class="my-6">
            <h1 class="text-3xl font-bold">Registrar empleado</h1>
            <p class="mt-2 text-slate-500">
                Completa los datos del nuevo integrante de la sucursal.
            </p>
        </header>

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                <p class="font-semibold">Revisa los siguientes errores:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('employees.store') }}"
              class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="employee_number" class="mb-2 block text-sm font-medium">
                        Número de empleado *
                    </label>
                    <input id="employee_number" name="employee_number"
                           value="{{ old('employee_number') }}" required maxlength="50"
                           class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="full_name" class="mb-2 block text-sm font-medium">
                        Nombre completo *
                    </label>
                    <input id="full_name" name="full_name"
                           value="{{ old('full_name') }}" required maxlength="150"
                           class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium">
                        Correo electrónico *
                    </label>
                    <input id="email" name="email" type="email"
                           value="{{ old('email') }}" required maxlength="255"
                           class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-sm font-medium">
                        Teléfono
                    </label>
                    <input id="phone" name="phone" type="tel"
                           value="{{ old('phone') }}" maxlength="30"
                           class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="position_id" class="mb-2 block text-sm font-medium">
                        Puesto laboral *
                    </label>
                    <select id="position_id" name="position_id" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 focus:border-blue-500 focus:outline-none">
                        <option value="">Selecciona un puesto</option>
                        @foreach ($puestos as $puesto)
                            <option value="{{ $puesto->id }}"
                                @selected(old('position_id') === $puesto->id)>
                                {{ $puesto->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="hire_date" class="mb-2 block text-sm font-medium">
                        Fecha de contratación *
                    </label>
                    <input id="hire_date" name="hire_date" type="date"
                           value="{{ old('hire_date') }}" required
                           class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="status" class="mb-2 block text-sm font-medium">
                        Estado *
                    </label>
                    <select id="status" name="status" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 focus:border-blue-500 focus:outline-none">
                        <option value="active" @selected(old('status', 'active') === 'active')>
                            Activo
                        </option>
                        <option value="inactive" @selected(old('status') === 'inactive')>
                            Inactivo
                        </option>
                        <option value="suspended" @selected(old('status') === 'suspended')>
                            Suspendido
                        </option>
                    </select>
                </div>
            </div>

            <p class="text-sm text-slate-500">* Campos obligatorios.</p>

            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('employees.index') }}"
                   class="rounded-lg border border-slate-300 px-5 py-3 hover:bg-slate-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
                    Guardar empleado
                </button>
            </div>
        </form>
    </main>
</body>
</html>
