<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin - UMAP</title>
  @vite(['resources/css/admin-login.css'])
</head>
<body class="login-body">

  <div class="login-card">
    <div class="login-header">
      <div class="login-logo">U</div>
      <div>
        <div class="login-title">Admin UMAP</div>
        <div class="login-sub">Masuk untuk kelola data menteri</div>
      </div>
    </div>

    @if ($errors->any())
      <div class="login-alert">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
      @csrf

      <label class="login-label">Email Admin</label>
      <input type="email" name="email" class="login-input"
             value="{{ old('email') }}" placeholder="admin@umap.local" required>

      <label class="login-label">Password</label>
      <input type="password" name="password" class="login-input"
             placeholder="••••••••" required>

      <label class="login-remember">
        <input type="checkbox" name="remember">
        Ingat saya
      </label>

      <button class="login-btn" type="submit">Masuk</button>
    </form>

    <div class="login-foot">
      &copy; {{ date('Y') }} UMAP Admin
    </div>
  </div>

</body>
</html>
