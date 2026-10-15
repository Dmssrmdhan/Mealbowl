<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Jenis pembayaran awal (bisa ditambah/diubah CEO dari aplikasi)
        foreach (['Tunai', 'Transfer', 'QRIS'] as $name) {
            PaymentType::firstOrCreate(['name' => $name]);
        }

        // Contoh menu (bisa diganti CEO dari aplikasi)
        $menus = [
            ['Nasi Goreng', 15000, 'Makanan'],
            ['Mie Ayam', 12000, 'Makanan'],
            ['Ayam Geprek', 18000, 'Makanan'],
            ['Es Teh', 5000, 'Minuman'],
            ['Es Jeruk', 7000, 'Minuman'],
            ['Kopi', 8000, 'Minuman'],
        ];
        foreach ($menus as [$name, $price, $category]) {
            Menu::firstOrCreate(['name' => $name], ['price' => $price, 'category' => $category, 'is_active' => true]);
        }

        // Akun awal -- SEGERA GANTI PASSWORD setelah login pertama.
        // Di luar environment lokal, password WAJIB diisi lewat SEED_CEO_PASSWORD & SEED_ADMIN_PASSWORD.
        $ceoPassword = env('SEED_CEO_PASSWORD');
        $adminPassword = env('SEED_ADMIN_PASSWORD');

        if (app()->environment('local', 'testing')) {
            $ceoPassword = $ceoPassword ?: '123';
            $adminPassword = $adminPassword ?: '123';
        } elseif (!$ceoPassword || !$adminPassword) {
            throw new \RuntimeException('Isi SEED_CEO_PASSWORD dan SEED_ADMIN_PASSWORD dulu sebelum menjalankan db:seed.');
        }

        User::firstOrCreate(['username' => 'ceo'], [
            'name' => 'CEO MealBowl',
            'password' => Hash::make($ceoPassword),
            'role' => User::ROLE_CEO,
        ]);
        User::firstOrCreate(['username' => 'admin'], [
            'name' => 'Admin',
            'password' => Hash::make($adminPassword),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
