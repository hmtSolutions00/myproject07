<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','UMAP Menteri')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark px-3 py-2" style="background:transparent;">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="{{ route('home') }}">
      <span style="color:var(--accent2)">UMAP</span> Menteri
    </a>
    <div class="d-flex gap-2">
      {{-- <a class="btn btn-sm btn-outline-light rounded-pill px-3" href="{{ route('compare') }}">Bandingkan</a>
      <a class="btn btn-sm btn-light rounded-pill px-3" href="{{ route('menteri.create') }}">Tambah Data</a> --}}
    </div>
  </div>
</nav>

<main class="container-fluid px-3 px-lg-4 pb-5">
  @yield('content')
</main>

<footer class="container-fluid px-3 px-lg-4 pt-4 pb-5 text-center" style="color:var(--muted)">
  <small>UMAP Menteri · Data visual untuk Gen-Z · v0.1</small>
</footer>

@stack('scripts')
</body>
</html>
