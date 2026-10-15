<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentTypeController extends Controller
{
    public function index()
    {
        return view('payment_types.index', [
            'types' => PaymentType::withCount('transactions')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:payment_types,name'],
        ], [
            'name.required' => 'Nama jenis pembayaran wajib diisi.',
            'name.unique' => 'Jenis pembayaran itu sudah ada.',
        ]);

        PaymentType::create($data);

        return back()->with('success', 'Jenis pembayaran ditambahkan.');
    }

    public function update(Request $request, PaymentType $paymentType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('payment_types', 'name')->ignore($paymentType->id)],
        ], [
            'name.required' => 'Nama jenis pembayaran wajib diisi.',
            'name.unique' => 'Jenis pembayaran itu sudah ada.',
        ]);

        $paymentType->update($data);

        return back()->with('success', 'Jenis pembayaran diperbarui.');
    }

    public function destroy(PaymentType $paymentType)
    {
        if ($paymentType->transactions()->exists()) {
            return back()->with('error', 'Jenis pembayaran ini sudah dipakai di transaksi, jadi tidak bisa dihapus.');
        }

        $paymentType->delete();

        return back()->with('success', 'Jenis pembayaran dihapus.');
    }
}
