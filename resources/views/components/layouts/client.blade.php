<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>{{ $title ?? 'Terminal de Tickets' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  @livewireStyles

  <style>
    /* Prevent any scrolling on the kiosk screen */
    html, body {
      height: 100%;
      margin: 0;
      overflow: hidden;
      touch-action: none;         /* disable touch-scrolling on touchscreens */
      overscroll-behavior: none;  /* prevent bounce/overscroll */
    }

    body {
      font-family: sans-serif;
      background: #f8f9fa;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 0; /* we don’t need extra padding if fullscreen */
    }
  </style>
</head>

<body>
  {{ $slot }}

  @livewireScripts
  @stack('scripts')
</body>
</html>
