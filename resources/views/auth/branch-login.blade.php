<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso a sucursal | Banco Central</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #172033;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 34px;
            border-radius: 18px;
            background: white;
            box-shadow: 0 20px 60px #0004;
        }

        .brand {
            color: #2563eb;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        h1 {
            margin-bottom: 8px;
            font-size: 27px;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 26px;
            line-height: 1.5;
        }

        label {
            display: block;
            margin: 17px 0 8px;
            font-weight: bold;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 15px;
        }

        input:focus {
            outline: 2px solid #93c5fd;
            border-color: #2563eb;
        }

        button {
            width: 100%;
            margin-top: 24px;
            padding: 14px;
            border: 0;
            border-radius: 9px;
            background: #2563eb;
            color: white;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .error {
            padding: 12px;
            border-radius: 8px;
            color: #991b1b;
            background: #fee2e2;
            font-size: 14px;
            line-height: 1.5;
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            color: #64748b;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <main class="login-card">
        <div class="brand">Banco Central</div>
        <h1>Acceso a sucursal</h1>
        <p class="subtitle">
            Identifica tu sucursal para ingresar al sistema administrativo.
        </p>

        @if ($errors->any())
            <div class="error" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf


            <label for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="gerente@sucursal.com"
                autocomplete="username" required autofocus>

            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" placeholder="Introduce tu contraseña"
                autocomplete="current-password" required>

            <button type="submit">Iniciar sesión</button>

        </form>

        <div class="footer">
            Acceso sujeto a la validación del Banco Central.
        </div>
    </main>
</body>

</html>