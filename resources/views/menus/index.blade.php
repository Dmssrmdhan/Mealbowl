@extends('layouts.app')

@section('title', 'Daftar Menu')
@section('heading', 'Daftar Menu')

@section('content')
    <div class="card">
        <div class="card-head">
            <h3>Daftar Menu & Harga</h3>
            @if(auth()->user()->isCeo())
                <a href="{{ route('menus.create') }}" class="btn btn-primary push-right"><svg class="i"><use href="#i-plus"/></svg> Tambah Menu</a>
            @endif
        </div>

        @if($menus->isEmpty())
            <div class="empty">Belum ada menu.</div>
        @else
            <div class="table-wrap">
                <table class="stack">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Menu</th>
                        <th class="right">Harga</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        @if(auth()->user()->isCeo())<th></th>@endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($menus as $m)
                        <tr>
                            <td data-label="No">{{ $loop->iteration }}</td>
                            <td data-label="Menu"><strong>{{ $m->name }}</strong></td>
                            <td data-label="Harga" class="right">@rp($m->price)</td>
                            <td data-label="Kategori">
                                <span class="badge {{ $m->category === 'Makanan' ? 'badge-food' : ($m->category === 'Minuman' ? 'badge-drink' : 'badge-snack') }}">{{ $m->category }}</span>
                            </td>
                            <td data-label="Status">
                                <span class="badge {{ $m->is_active ? 'badge-on' : 'badge-off' }}">{{ $m->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            @if(auth()->user()->isCeo())
                                <td class="act">
                                    <div class="actions">
                                        <a class="icon-btn" href="{{ route('menus.edit', $m) }}" title="Edit"><svg class="i"><use href="#i-edit"/></svg></a>
                                        <form method="POST" action="{{ route('menus.destroy', $m) }}" onsubmit="return confirm('Yakin hapus menu ini?')">
                                            @csrf @method('DELETE')
                                            <button class="icon-btn danger" title="Hapus"><svg class="i"><use href="#i-trash"/></svg></button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
