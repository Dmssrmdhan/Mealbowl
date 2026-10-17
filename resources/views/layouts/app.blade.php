<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — MealBowl</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/mealbowl.css') }}">
</head>
<body>
@include('partials.icons')
@php $u = auth()->user(); @endphp

<div class="app">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <span class="logo"><svg class="i"><use href="#i-bowl"/></svg></span>
            MealBowl
        </div>

        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="i"><use href="#i-home"/></svg> Dashboard
            </a>
            <<a href="{{ route('sales.create') }}" class="{{ request()->routeIs('sales.create') ? 'active' : '' }}">
                <svg class="i"><use href="#i-edit"/></svg> Input Penjualan
            </a>
            <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">
                <svg class="i"><use href="#i-list"/></svg> Riwayat Transaksi
            </a>
            <a href="{{ route('menus.index') }}" class="{{ request()->routeIs('menus.*') ? 'active' : '' }}">
                <svg class="i"><use href="#i-list"/></svg> Daftar Menu
            </a>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="i"><use href="#i-chart"/></svg> Laporan
            </a>

            @if($u->isCeo())
                <div class="sep">Khusus CEO</div>
                <a href="{{ route('payment_types.index') }}" class="{{ request()->routeIs('payment_types.*') ? 'active' : '' }}">
                    <svg class="i"><use href="#i-card"/></svg> Jenis Pembayaran
                </a>
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <svg class="i"><use href="#i-users"/></svg> Kelola Akun
                </a>
            @endif
        </nav>

        <div class="userbox">
            <div class="avatar">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</div>
            <div class="who">
                <strong>{{ $u->name }}</strong>
                <span>{{ strtoupper($u->role) }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Keluar"><svg class="i"><use href="#i-logout"/></svg></button>
            </form>
        </div>
    </aside>
    <div class="overlay" id="overlay"></div>

    <main class="main">
        <header class="topbar">
            <button class="menu-btn" id="menuBtn" type="button" aria-label="Buka menu"><svg class="i" style="width:24px;height:24px"><use href="#i-hamburger"/></svg></button>
            <h1>@yield('heading', 'Dashboard')</h1>
            <span class="date">@hari(now())</span>
        </header>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script>
    (function () {
        var sb = document.getElementById('sidebar'), ov = document.getElementById('overlay');
        function toggle(open) { sb.classList.toggle('open', open); ov.classList.toggle('show', open); }
        document.getElementById('menuBtn').addEventListener('click', function () { toggle(!sb.classList.contains('open')); });
        ov.addEventListener('click', function () { toggle(false); });
    })();
</script>
@stack('scripts')
</body>
</html>
