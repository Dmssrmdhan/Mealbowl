<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\PaymentType;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /** Riwayat penjualan (default: hari ini). ?date=2026-10-15 atau ?date=all */
    public function index(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $all = $date === 'all';

        if (!$all) {
            try {
                $date = Carbon::createFromFormat('!Y-m-d', (string) $date)->toDateString();
            } catch (\Throwable $e) {
                $date = Carbon::today()->toDateString();
            }
        }

        $query = Transaction::with(['paymentType', 'user', 'details.menu'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if (!$all) {
            $query->whereDate('date', $date);
        }

        $transactions = $query->paginate(15)->withQueryString();

        $total = Transaction::when(!$all, function ($q) use ($date) {
            $q->whereDate('date', $date);
        })->sum('total');

        return view('sales.index', [
            'transactions' => $transactions,
            'date' => $date,
            'all' => $all,
            'total' => (int) $total,
        ]);
    }

    public function create()
    {
        $menus = Menu::where('is_active', true)->orderBy('category')->orderBy('name')->get();

        return view('sales.create', [
            'menuList' => $menus->map(function ($m) {
                return ['id' => $m->id, 'name' => $m->name, 'price' => $m->price];
            })->values(),
            'paymentTypes' => PaymentType::orderBy('id')->get(),
            'today' => Carbon::today()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'payment_type_id' => ['required', 'exists:payment_types,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'distinct', 'exists:menus,id'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:9999'],
        ], [
            'date.required' => 'Tanggal wajib diisi.',
            'date.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
            'payment_type_id.required' => 'Pilih jenis pembayaran.',
            'payment_type_id.exists' => 'Jenis pembayaran tidak valid.',
            'items.required' => 'Tambahkan minimal satu menu.',
            'items.min' => 'Tambahkan minimal satu menu.',
            'items.*.menu_id.required' => 'Pilih menu pada setiap baris.',
            'items.*.menu_id.distinct' => 'Ada menu yang dipilih dua kali. Gabungkan jumlahnya di satu baris.',
            'items.*.menu_id.exists' => 'Menu tidak ditemukan.',
            'items.*.qty.required' => 'Jumlah wajib diisi.',
            'items.*.qty.integer' => 'Jumlah harus berupa angka bulat.',
            'items.*.qty.min' => 'Jumlah minimal 1.',
        ]);

        // Harga selalu diambil dari database (bukan dari kiriman browser)
        $menus = Menu::whereIn('id', collect($data['items'])->pluck('menu_id'))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        if ($menus->count() !== count($data['items'])) {
            return back()->withInput()
                ->withErrors(['items' => 'Ada menu yang sudah nonaktif. Silakan pilih ulang.']);
        }

        DB::transaction(function () use ($data, $menus, $request) {
            $total = 0;
            $lines = [];

            foreach ($data['items'] as $item) {
                $menu = $menus->get((int) $item['menu_id']);
                $qty = (int) $item['qty'];
                $subtotal = $menu->price * $qty;
                $total += $subtotal;

                $lines[] = [
                    'menu_id' => $menu->id,
                    'qty' => $qty,
                    'price' => $menu->price,
                    'subtotal' => $subtotal,
                ];
            }

            $transaction = Transaction::create([
                'date' => $data['date'],
                'total' => $total,
                'payment_type_id' => $data['payment_type_id'],
                'user_id' => $request->user()->id,
            ]);

            $transaction->details()->createMany($lines);
        });

        return redirect()
            ->route('sales.index', ['date' => $data['date']])
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete(); // detail ikut terhapus (cascade)

        return back()->with('success', 'Transaksi dihapus.');
    }
}
