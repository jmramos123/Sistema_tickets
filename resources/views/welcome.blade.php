<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página de Bienvenida</title>
    <style>
        body {
            background: #f8fafc;
            font-family: 'Nunito', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .welcome-card {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .welcome-card h1 {
            font-size: 3rem;
            color: #111;
            margin-bottom: 1rem;
        }
        .welcome-card p {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 2rem;
        }
        .welcome-card a {
            text-decoration: none;
            padding: 0.75rem 2rem;
            background: #4f46e5;
            color: white;
            border-radius: 0.5rem;
            font-size: 1rem;
        }
        .welcome-card a:hover {
            background: #4338ca;
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <h1>Bienvenido al Sistema de Turnos</h1>
        <p>Por favor inicie sesión para continuar.</p>
        <a href="{{ route('login') }}">Iniciar Sesión</a>
    </div>
</body>
</html>
