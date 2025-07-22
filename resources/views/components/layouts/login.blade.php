<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $title ?? 'Login' }}</title>

  {{-- Tailwind/Vite & Livewire --}}
  @vite(['resources/css/app.css','resources/js/app.js'])
  @livewireStyles

  {{-- AdminLTE CSS --}}
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
  
  @stack('styles')
</head>
<body class="hold-transition login-page">

  {{-- This slot should output the .login-box markup --}}
  {{ $slot }}

  {{-- Livewire scripts --}}
  @livewireScripts

  {{-- jQuery & Bootstrap (required by AdminLTE) --}}
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  {{-- AdminLTE JS --}}
  <script
    src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>

  @stack('scripts')
</body>
</html>
