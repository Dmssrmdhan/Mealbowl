@extends('layouts.app')

@php $editing = $menu->exists; @endphp

@section('title', $editing ? 'Edit Menu' : 'Tambah Menu')
@section('heading', $editing ? 'Edit Menu' : 'Tambah Menu')

@section('content')
    <div class="card" style="max-width:520px">
        <form method="POST" action="{{ $editing ? route('menus.update', $menu) : route('menus.store') }}">
            @csrf
            @if($editing) @method('PUT') @endif

            <div class="field">
                <label for="name">Nama Menu</label>
                <input type="text" id="name" name="name" value="{{ old('name', $menu->name) }}" required maxlength="100" autofocus>
            </div>

            <div class="field">
                <label for="price">Harga (Rp)</label>
                <input type="number" id="price" name="price" value="{{ old('price', $menu->price) }}" min="0" step="1" required>
                <div class="hint">Isi angka saja, contoh: 15000</div>
            </div>

            <div class="field">
                <label for="category">Kategori</label>
                <select id="category" name="category" required>
                    @foreach(\App\Models\Menu::CATEGORIES as $c)
                        <option value="{{ $c }}" @selected(old('category', $menu->category) === $c)>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <input type="hidden" name="is_active" value="0">
                <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $menu->is_active))> Menu aktif (tampil di input penjualan)</label>
            </div>

            <div class="inline-form">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-outline" href="{{ route('menus.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
