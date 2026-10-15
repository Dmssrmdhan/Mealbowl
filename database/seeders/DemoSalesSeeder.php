<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\PaymentType;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * OPSIONAL: isi data penjualan contoh 14 hari terakhir supaya grafik & laporan terlihat.
 * Jalankan:  php artisan db:seed --class=DemoSalesSeeder
 * Jangan dijalankan di data asli.
 */
class DemoSalesSeeder extends Seeder
{
    public function run()
    {
        $menus = Menu::where('is_active', true)->get();
        $payments = PaymentType::all();
        $user = User::where('role', User::ROLE_ADMIN)->first();

        if ($menus->isEmpty() || $payments->isEmpty()) {
            $this->command->warn('Jalankan php artisan db:seed dulu (menu & jenis bayar kosong).');
            return;
        }

        mt_srand(42);

        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $orders = mt_rand(3, 9);

            for ($o = 0; $o < $orders; $o++) {
                $items = $menus->shuffle()->take(mt_rand(1, 3));
                $total = 0;
                $lines = [];
                foreach ($items as $menu) {
                    $qty = mt_rand(1, 4);
                    $sub = $menu->price * $qty;
                    $total += $sub;
                    $lines[] = ['menu_id' => $menu->id, 'qty' => $qty, 'price' => $menu->price, 'subtotal' => $sub];
                }

                $tx = Transaction::create([
                    'date' => $date->toDateString(),
                    'total' => $total,
                    'payment_type_id' => $payments->random()->id,
                    'user_id' => $user ? $user->id : null,
                ]);
                $tx->details()->createMany($lines);
            }
        }

        $this->command->info('Data penjualan contoh berhasil dibuat.');
    }
}
