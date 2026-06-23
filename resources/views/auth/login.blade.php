<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Master Jaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #1F3349; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #fff; border-radius: 1rem; max-width: 420px; width: 100%; padding: 2.5rem; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        .login-title { color: #1F3349; font-weight: 700; }
        .login-subtitle { color: #D99A1B; font-weight: 600; }
        .btn-login { background-color: #1F3349; color: #fff; font-size: 1.1rem; padding: 0.75rem; }
        .btn-login:hover { background-color: #162840; color: #fff; }
        .form-control:focus { border-color: #D99A1B; box-shadow: 0 0 0 0.2rem rgba(217,154,27,0.25); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <i class="bi bi-shop" style="font-size: 3rem; color: #D99A1B;"></i>
            <h2 class="login-title mt-2">Master Jaya</h2>
            <p class="login-subtitle">Sistem Toko Sembako</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2">
                @foreach($errors->all() as $error) <small>{{ $error }}</small> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" required autofocus placeholder="Masukkan email">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control form-control-lg" required placeholder="Masukkan password">
                </div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
