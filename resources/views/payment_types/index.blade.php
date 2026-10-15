@extends('layouts.app')

@section('title', 'Jenis Pembayaran')
@section('heading', 'Jenis Pembayaran')

@section('content')
    <div class="grid-2">
        <div class="card">
            <h3>Daftar Jenis Pembayaran</h3>
            <div class="table-wrap">
                <table class="stack">
                    <thead><tr><th>Nama</th><th class="right">Dipakai</th><th></th></tr></thead>
                    <tbody>
                    @foreach($types as $t)
                        <tr>
                            <td data-label="Nama">
                                <form method="POST" action="{{ route('payment_types.update', $t) }}" class="inline-form">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $t->name }}" maxlength="50" required>
                                    <button class="btn btn-outline" type="submit">Simpan</button>
                                </form>
                            </td>
                            <td data-label="Dipakai" class="right">{{ $t->transactions_count }}x</td>
                            <td class="act">
                                <form method="POST" action="{{ route('payment_types.destroy', $t) }}" onsubmit="return confirm('Yakin hapus jenis pembayaran ini?')">
                                    @csrf @method('DELETE')
                                    <button class="icon-btn danger" title="Hapus"><svg class="i"><use href="#i-trash"/></svg></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3>Tambah Jenis Pembayaran</h3>
            <form method="POST" action="{{ route('payment_types.store') }}">
                @csrf
                <div class="field">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="name" placeholder="contoh: E-Wallet" maxlength="50" required>
                </div>
                <button class="btn btn-primary" type="submit"><svg class="i"><use href="#i-plus"/></svg> Tambah</button>
            </form>
        </div>
    </div>
@endsection
