<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TV Display</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite([
    'resources/css/app.css',
    'resources/js/app.js',    {{-- ← add this --}}
    ])
    @livewireStyles
    <style>
    :root {
        --called-bg: #fff;
        --called-color: #111;
    }

    .called-card {
        background-color: var(--called-bg);
        color: var(--called-color);
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 0 30px rgba(255, 255, 255, 0.2);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s ease;
    }

    @keyframes green-flicker-sequence {
        0%, 100% {
            background-color: #fff;
            color: #111;
        }
        25%, 75% {
            background-color: #28ff28;
            color: #000;
        }
        50% {
            background-color: #fff;
            color: #111;
        }
    }

    .called-card.flicker {
        animation: green-flicker-sequence 1.5s ease-in-out;
    }

    .called-card h1 {
        font-size: 5rem;
    }

    .called-card h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .called-card h3 {
        font-size: 3rem;
        text-align:center;
    }
    
    .fw-ultra-bold {
        font-weight: 900;
    }

    .ticket-card {
        min-width: 250px;
        height: 120px;
        padding: 1.5rem;
        font-size: 3rem;
        flex: 0 0 auto;
        background-color: #fafafa;
        color: #111;
        border-radius: 1rem;
        border: 1px solid #ccc;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .ticket-card {
            min-width: 12rem;
            height: 6rem;
            font-size: 2rem;
        }
    }
        html, body {
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            height: 100%;
        }

        body {
            overflow: hidden;
        }

        main {
            height: 100vh;
            width: 100vw;
        }
    </style>
</head>
<body>

    <main>
        {{ $slot }}
        
    </main>
    <audio id="bip-sound" src="/sounds/bip.mp3" preload="auto"></audio>

    @livewireScripts
    @stack('scripts')
</body>
</html>
