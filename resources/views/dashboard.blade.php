@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <div class="card welcome">
        <div class="avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
        <div>
            <strong>Halo, {{ auth()->user()->name }}</strong><br>
            <span class="muted">Selamat datang di aplikasi laporan penjualan MealBowl.</span>
        </div>
        <a href="{{ route('sales.create') }}" class="btn btn-primary push-right"><svg class="i"><use href="#i-plus"/></svg> Input Penjualan</a>
    </div>

    <div class="stats">
        <div class="stat stat-primary">
            <span>Total Pendapatan Hari Ini</span>
            <strong>@rp($income)</strong>
            @if(!is_null($pct))
                <small>{{ $pct >= 0 ? '▲' : '▼' }} {{ abs($pct) }}% dari kemarin</small>
            @else
                <small>Belum ada data kemarin</small>
            @endif
        </div>
        <div class="stat"><span>Total Order</span><strong>{{ $orders }}</strong><small>transaksi hari ini</small></div>
        <div class="stat"><span>Menu Terjual</span><strong>{{ $pcs }}</strong><small>pcs hari ini</small></div>
        <div class="stat"><span>Jenis Bayar</span><strong>{{ $paymentTypeCount }}</strong><small>metode tersedia</small></div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h3>Grafik Pendapatan (7 Hari Terakhir)</h3>
            @include('partials.bars', ['series' => $series])
        </div>

        <div class="card">
            <h3>Menu Terlaris (Bulan Ini)</h3>
            @if($topMenus->isEmpty())
                <div class="empty">Belum ada penjualan bulan ini.</div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Menu</th><th class="right">Terjual</th></tr></thead>
                        <tbody>
                        @foreach($topMenus as $m)
                            <tr><td>{{ $m->name }}</td><td class="right">{{ $m->qty }} pcs</td></tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
