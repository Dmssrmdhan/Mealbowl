@extends('layouts.app')

@section('title', 'Laporan Pendapatan')
@section('heading', 'Laporan Pendapatan')

@section('content')
    @php
        $tabs = ['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'yearly' => 'Tahunan'];
        $avg = $summary['orders'] > 0 ? (int) round($summary['total'] / $summary['orders']) : 0;

        $palette = ['#2563eb', '#16a34a', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4'];
        $payTotal = max(1, (float) $summary['byPayment']->sum('total'));
        $acc = 0;
        $stops = [];
        foreach ($summary['byPayment'] as $n => $p) {
            $start = $acc;
            $acc += $p->total / $payTotal * 100;
            $stops[] = $palette[$n % count($palette)] . ' ' . round($start, 2) . '% ' . round($acc, 2) . '%';
        }
        $gradient = count($stops) ? implode(', ', $stops) : '#e4e9f2 0% 100%';
    @endphp

    <div class="tabs">
        @foreach($tabs as $key => $text)
            <a class="tab {{ $type === $key ? 'active' : '' }}" href="{{ route('reports.index', ['type' => $key, 'date' => $date->toDateString()]) }}">{{ $text }}</a>
        @endforeach
    </div>

    <form method="GET" class="filter">
        <input type="hidden" name="type" value="{{ $type }}">
        <div class="field">
            <label for="date">Tanggal acuan</label>
            <input type="date" id="date" name="date" value="{{ $date->toDateString() }}">
        </div>
        <button class="btn btn-primary" type="submit">Cari</button>
        <button class="btn btn-outline" type="button" onclick="window.print()">Cetak</button>
    </form>

    <p class="period">Periode: <strong>{{ $label }}</strong></p>

    <div class="stats">
        <div class="stat stat-primary"><span>Total Pendapatan</span><strong>@rp($summary['total'])</strong></div>
        <div class="stat"><span>Jumlah Order</span><strong>{{ $summary['orders'] }}</strong></div>
        <div class="stat"><span>Menu Terjual</span><strong>{{ $summary['pcs'] }}</strong><small>pcs</small></div>
        <div class="stat"><span>Rata-rata per Order</span><strong>@rp($avg)</strong></div>
    </div>

    <div class="grid-eq">
        <div class="card">
            @if($series)
                <h3>Grafik Pendapatan</h3>
                @include('partials.bars', ['series' => $series])
            @else
                <h3>Transaksi Hari Ini</h3>
                @if($transactions->isEmpty())
                    <div class="empty">Belum ada transaksi pada tanggal ini.</div>
                @else
                    <div class="table-wrap">
                        <table class="stack">
                            <thead><tr><th>Jam</th><th>Pesanan</th><th>Bayar</th><th class="right">Total</th></tr></thead>
                            <tbody>
                            @foreach($transactions as $t)
                                <tr>
                                    <td data-label="Jam">{{ $t->created_at->format('H:i') }}</td>
                                    <td data-label="Pesanan">{{ $t->details->map(function ($d) { return $d->menu->name . ' ×' . $d->qty; })->implode(', ') }}</td>
                                    <td data-label="Bayar">{{ $t->paymentType->name }}</td>
                                    <td data-label="Total" class="right">@rp($t->total)</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        </div>

        <div class="card">
            <h3>Jenis Pembayaran</h3>
            @if($summary['byPayment']->isEmpty())
                <div class="empty">Belum ada data.</div>
            @else
                <div class="donut-wrap">
                    <div class="donut" style="background: conic-gradient({{ $gradient }})"><div class="donut-hole"></div></div>
                    <ul class="legend">
                        @foreach($summary['byPayment'] as $n => $p)
                            <li>
                                <span class="dot" style="background: {{ $palette[$n % count($palette)] }}"></span>
                                {{ $p->name }}
                                <span class="val">@rp($p->total) <span class="muted">({{ round($p->total / $payTotal * 100) }}%)</span></span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt">
        <h3>Rincian Pendapatan per Menu</h3>
        @if($summary['byMenu']->isEmpty())
            <div class="empty">Belum ada penjualan pada periode ini.</div>
        @else
            <div class="table-wrap">
                <table class="stack">
                    <thead><tr><th>No</th><th>Menu</th><th class="right">Terjual (pcs)</th><th class="right">Pendapatan</th></tr></thead>
                    <tbody>
                    @foreach($summary['byMenu'] as $m)
                        <tr>
                            <td data-label="No">{{ $loop->iteration }}</td>
                            <td data-label="Menu">{{ $m->name }}</td>
                            <td data-label="Terjual" class="right">{{ $m->qty }}</td>
                            <td data-label="Pendapatan" class="right">@rp($m->total)</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr><td></td><td>Total</td><td class="right">{{ $summary['pcs'] }}</td><td class="right">@rp($summary['total'])</td></tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
@endsection
