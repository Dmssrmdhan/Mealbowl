<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index()
    {
        return view('menus.index', [
            'menus' => Menu::orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('menus.form', [
            'menu' => new Menu(['category' => 'Makanan', 'is_active' => true]),
        ]);
    }

    public function store(Request $request)
    {
        Menu::create($this->validated($request));

        return redirect()->route('menus.index')->with('success', 'Menu ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        return view('menus.form', ['menu' => $menu]);
    }

    public function update(Request $request, Menu $menu)
    {
        $menu->update($this->validated($request));

        return redirect()->route('menus.index')->with('success', 'Menu diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->details()->exists()) {
            return back()->with('error', 'Menu ini sudah pernah terjual, jadi tidak bisa dihapus. Nonaktifkan saja lewat tombol edit.');
        }

        $menu->delete();

        return back()->with('success', 'Menu dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'category' => ['required', Rule::in(Menu::CATEGORIES)],
        ], [
            'name.required' => 'Nama menu wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka bulat (tanpa titik/koma).',
            'category.in' => 'Kategori tidak valid.',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
