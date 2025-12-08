<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin UMAP Menteri</title>

  @vite([
    'resources/css/admin.css',
    'resources/js/app.js'
  ])
</head>

<body class="admin-body">
  <div class="admin-shell">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
      <div class="brand">
        <div style="width:34px;height:34px;border-radius:10px;background:#00A3FF;display:grid;place-items:center;font-weight:900;color:white;">U</div>
        <div>
          Admin UMAP
          <div style="font-size:.8rem;color:#94a3b8;font-weight:700;">Kelola data menteri</div>
        </div>
      </div>

      <nav class="nav flex-column gap-1">
        <a class="nav-link {{ request()->routeIs('admin.detail_menteri.*')?'active':'' }}"
           href="{{ route('admin.detail_menteri.index') }}">Untuk Aplikasi</a>

        <a class="nav-link {{ request()->routeIs('admin.aplikasi.*')?'active':'' }}"
           href="{{ route('admin.aplikasi.index') }}">Untuk UMAP</a>

        <a class="nav-link {{ request()->routeIs('admin.umap_integer.*')?'active':'' }}"
           href="{{ route('admin.umap_integer.index') }}">Untuk UMAP Integer</a>
      </nav>
      <br>
       <div class="d-flex gap-2">
          <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold">
            Lihat Homepage
          </a>
        </div>
    </aside>

    {{-- CONTENT --}}
    <main>
      <div class="admin-topbar">
        <div>
          <div class="admin-title">@yield('page-title')</div>
          <div class="admin-sub">@yield('page-subtitle')</div>
        </div>

        <div class="d-flex gap-2">
          <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold">
            Lihat Homepage
          </a>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success rounded-3 fw-bold">
          {{ session('success') }}
        </div>
      @endif

      @yield('admin-content')
    </main>

  </div>
</body>
</html>
