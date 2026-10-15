@extends('layouts.app')

@php $editing = $user->exists; @endphp

@section('title', $editing ? 'Edit Akun' : 'Tambah Akun')
@section('heading', $editing ? 'Edit Akun' : 'Tambah Akun')

@section('content')
    <div class="card" style="max-width:520px">
        <form method="POST" action="{{ $editing ? route('users.update', $user) : route('users.store') }}" autocomplete="off">
            @csrf
            @if($editing) @method('PUT') @endif

            <div class="field">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="100" autofocus>
            </div>

            <div class="field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required maxlength="50">
            </div>

            <div class="field">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>ADMIN (input penjualan, lihat menu & laporan)</option>
                    <option value="ceo" @selected(old('role', $user->role) === 'ceo')>CEO (akses penuh)</option>
                </select>
            </div>

            <div class="field">
                <label for="password">Password {{ $editing ? '(kosongkan jika tidak diganti)' : '' }}</label>
                <input type="password" id="password" name="password" minlength="6" autocomplete="new-password" {{ $editing ? '' : 'required' }}>
                <div class="hint">Minimal 6 karakter.</div>
            </div>

            <div class="field">
                <label for="password_confirmation">Ulangi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
            </div>

            <div class="inline-form">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-outline" href="{{ route('users.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
