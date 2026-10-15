<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — MealBowl</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/mealbowl.css') }}">
</head>
<body>
@include('partials.icons')
<div class="login-page">
    <form class="login-card" method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div class="logo"><svg class="i"><use href="#i-bowl"/></svg></div>
        <h1>MealBowl</h1>
        <p class="sub">Aplikasi Laporan Penjualan<br>Silakan masuk untuk melanjutkan</p>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="field">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username" autocomplete="username" autofocus required>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="pw">
                <input id="password" type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                <button type="button" id="togglePw">Lihat</button>
            </div>
        </div>

        <label class="check" style="margin-bottom:16px"><input type="checkbox" name="remember" value="1"> Ingat saya</label>

        <button class="btn btn-primary btn-block" type="submit">Login</button>

    </form>
</div>
<script>
    document.getElementById('togglePw').addEventListener('click', function () {
        var p = document.getElementById('password');
        var show = p.type === 'password';
        p.type = show ? 'text' : 'password';
        this.textContent = show ? 'Sembunyi' : 'Lihat';
    });
</script>
</body>
</html>
