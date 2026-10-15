@extends('layouts.app')

@section('title', 'Kelola Akun')
@section('heading', 'Kelola Akun')

@section('content')
    <div class="card">
        <div class="card-head">
            <h3>Akun Pengguna</h3>
            <a href="{{ route('users.create') }}" class="btn btn-primary push-right"><svg class="i"><use href="#i-plus"/></svg> Tambah Akun</a>
        </div>

        <div class="table-wrap">
            <table class="stack">
                <thead><tr><th>Nama</th><th>Username</th><th>Role</th><th></th></tr></thead>
                <tbody>
                @foreach($users as $u)
                    <tr>
                        <td data-label="Nama"><strong>{{ $u->name }}</strong></td>
                        <td data-label="Username">{{ $u->username }}</td>
                        <td data-label="Role"><span class="badge {{ $u->isCeo() ? 'badge-ceo' : 'badge-admin' }}">{{ strtoupper($u->role) }}</span></td>
                        <td class="act">
                            <div class="actions">
                                <a class="icon-btn" href="{{ route('users.edit', $u) }}" title="Edit"><svg class="i"><use href="#i-edit"/></svg></a>
                                @if($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('Yakin hapus akun ini?')">
                                        @csrf @method('DELETE')
                                        <button class="icon-btn danger" title="Hapus"><svg class="i"><use href="#i-trash"/></svg></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
