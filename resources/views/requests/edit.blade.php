<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales | Editar solicitud</title>
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

        <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-slate-500">Menú principal</p>

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
        <header class="mb-8">
            <p class="text-sm text-slate-500">Administración / Solicitudes / Editar</p>
            <h2 class="mt-1 text-3xl font-bold text-slate-900">Editar solicitud</h2>
            <p class="mt-2 text-slate-500">Modifica la información y guarda los cambios.</p>
        </header>

        <section class="max-w-4xl rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-8">
            <form action="{{ route('requests.update', $solicitud->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('requests._form', ['boton' => 'Guardar cambios'])
            </form>
        </section>
    </main>
</div>
</body>
</html>
