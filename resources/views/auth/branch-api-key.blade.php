<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincular API Key | Sucursales</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 20px; font-family: Arial, sans-serif; background: #0f172a; color: #172033; }
        .card { width: 100%; max-width: 480px; padding: 34px; border-radius: 18px; background: #fff; box-shadow: 0 20px 60px #0004; }
        .brand { color: #2563eb; font-size: 13px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; }
        h1 { margin: 12px 0 8px; font-size: 26px; }
        .subtitle { color: #64748b; margin: 0 0 22px; line-height: 1.5; }
        .branch { padding: 12px 14px; border-radius: 9px; background: #eff6ff; color: #1e40af; font-weight: bold; margin-bottom: 20px; }
        label { display: block; margin: 16px 0 8px; font-weight: bold; font-size: 14px; }
        input { width: 100%; padding: 13px; border: 1px solid #cbd5e1; border-radius: 9px; font-size: 14px; }
        input:focus { outline: 2px solid #93c5fd; border-color: #2563eb; }
        button { width: 100%; margin-top: 22px; padding: 14px; border: 0; border-radius: 9px; background: #2563eb; color: white; font-weight: bold; font-size: 15px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .error { padding: 12px; border-radius: 8px; color: #991b1b; background: #fee2e2; font-size: 14px; line-height: 1.5; margin-bottom: 16px; }
        .note { margin-top: 18px; color: #64748b; font-size: 12px; line-height: 1.5; }
        .back { display: block; margin-top: 18px; text-align: center; color: #2563eb; font-size: 14px; text-decoration: none; }
    </style>
</head>
<body>
    <main class="card">
        <div class="brand">Banco Central · Sucursales</div>
        <h1>Vincular API Key</h1>
        <p class="subtitle">Las credenciales de Supabase fueron aceptadas. Para confirmar el acceso, introduce la API Key completa que se mostró al crear esta sucursal en el Banco Central.</p>
        <div class="branch">Sucursal: {{ $branchCode }}</div>
        @if ($errors->any())
            <div class="error" role="alert">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('branch.key.submit') }}">
            @csrf
            <label for="email">Cuenta autenticada</label>
            <input id="email" type="email" value="{{ $userEmail }}" readonly>
            <label for="api_key">API Key completa de la sucursal</label>
            <input id="api_key" name="api_key" type="password" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="Pega aquí la API Key completa" required autofocus>
            <button type="submit">Validar sucursal y continuar</button>
        </form>
        <p class="note">La clave se valida con el Banco Central y se guarda cifrada solo si coincide con {{ $branchCode }} y la sucursal está activa. No se almacenará si la validación falla.</p>
        <a class="back" href="{{ route('login') }}">Volver al inicio de sesión</a>
    </main>
</body>
</html>
