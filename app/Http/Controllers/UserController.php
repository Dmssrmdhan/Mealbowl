<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'users' => User::orderBy('role')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('users.form', ['user' => new User(['role' => User::ROLE_ADMIN])]);
    }

    public function store(Request $request)
    {
        $request->merge(['username' => Str::lower(trim((string) $request->input('username')))]);
        $data = $request->validate($this->rules(null, true), $this->messages());

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'Akun ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.form', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $request->merge(['username' => Str::lower(trim((string) $request->input('username')))]);
        $data = $request->validate($this->rules($user, false), $this->messages());

        // Jangan sampai tidak ada CEO sama sekali
        if ($user->isCeo() && $data['role'] !== User::ROLE_CEO
            && User::where('role', User::ROLE_CEO)->count() <= 1) {
            return back()->withInput()->with('error', 'Harus ada minimal satu akun CEO.');
        }

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->role = $data['role'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return redirect()->route('users.index')->with('success', 'Akun diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun yang sedang dipakai.');
        }

        if ($user->isCeo() && User::where('role', User::ROLE_CEO)->count() <= 1) {
            return back()->with('error', 'Harus ada minimal satu akun CEO.');
        }

        $user->delete();

        return back()->with('success', 'Akun dihapus.');
    }

    private function rules(?User $user, bool $passwordRequired): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => [
                'required', 'alpha_dash', 'max:50',
                Rule::unique('users', 'username')->ignore($user ? $user->id : null),
            ],
            'role' => ['required', Rule::in([User::ROLE_CEO, User::ROLE_ADMIN])],
            'password' => [$passwordRequired ? 'required' : 'nullable', 'string', 'min:6', 'confirmed'],
        ];
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, dan garis bawah.',
            'username.unique' => 'Username sudah dipakai.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ];
    }
}
