@extends('layouts.app')

@section('title', 'Riwayat Penjualan')
@section('heading', 'Riwayat Penjualan')

@section('content')
    <form method="GET" class="filter">
        <div class="field">
            <label for="date">Tanggal</label>
            <input type="date" id="date" name="date" value="{{ $all ? '' : $date }}">
        </div>
        <button class="btn btn-primary" type="submit">Cari</button>
        <a class="btn btn-outline" href="{{ route('sales.index', ['date' => 'all']) }}">Semua</a>
        <a class="btn btn-primary push-right" href="{{ route('sales.create') }}"><svg class="i"><use href="#i-plus"/></svg> Input Penjualan</a>
    </form>

    <div class="stats" style="grid-template-columns:repeat(2,minmax(0,1fr));max-width:560px">
        <div class="stat stat-primary">
            <span>{{ $all ? 'Total Semua Transaksi' : 'Total Pendapatan' }}</span>
            <strong>@rp($total)</strong>
            @unless($all)<small>@hari($date)</small>@endunless
        </div>
        <div class="stat"><span>Jumlah Order</span><strong>{{ $transactions->total() }}</strong></div>
    </div>

    <div class="card">
        @if($transactions->isEmpty())
            <div class="empty">Belum ada transaksi{{ $all ? '' : ' pada tanggal ini' }}.</div>
        @else
            <div class="table-wrap">
                <table class="stack">
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pesanan</th>
                        <th>Bayar</th>
                        <th class="right">Total</th>
                        <th>Diinput</th>
                        @if(auth()->user()->isCeo())<th></th>@endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $t)
                        <tr>
                            <td data-label="Tanggal">@tgl($t->date)<br><span class="muted">{{ $t->created_at->format('H:i') }}</span></td>
                            <td data-label="Pesanan">{{ $t->details->map(function ($d) { return $d->menu->name . ' ×' . $d->qty; })->implode(', ') }}</td>
                            <td data-label="Bayar">{{ $t->paymentType->name }}</td>
                            <td data-label="Total" class="right"><strong>@rp($t->total)</strong></td>
                            <td data-label="Diinput">{{ $t->user ? $t->user->name : '-' }}</td>
                            @if(auth()->user()->isCeo())
                                <td class="act">
                                    <form method="POST" action="{{ route('sales.destroy', $t) }}" onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                        @csrf @method('DELETE')
                                        <button class="icon-btn danger" title="Hapus"><svg class="i"><use href="#i-trash"/></svg></button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        @endif
    </div>
@endsection
