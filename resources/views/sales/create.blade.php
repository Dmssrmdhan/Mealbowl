@extends('layouts.app')

@section('title', 'Input Penjualan')
@section('heading', 'Input Penjualan')

@section('content')
    @if($menuList->isEmpty())
        <div class="card empty">
            Belum ada menu aktif.
            @if(auth()->user()->isCeo())
                <a href="{{ route('menus.create') }}">Tambah menu dulu</a>.
            @else
                Minta CEO menambahkan menu terlebih dahulu.
            @endif
        </div>
    @else
        <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
            @csrf
            <div class="grid-2">
                <div class="card">
                    <div class="field" style="max-width:240px">
                        <label for="date">Tanggal</label>
                        <input type="date" id="date" name="date" value="{{ old('date', $today) }}" max="{{ $today }}" required>
                    </div>

                    <h3>Pilih Menu</h3>
                    <div class="table-wrap">
                        <table class="stack" id="itemsTable">
                            <thead>
                            <tr>
                                <th style="min-width:180px">Menu</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th class="right">Subtotal</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="rows"></tbody>
                        </table>
                    </div>
                    <button type="button" id="addRow" class="btn btn-outline mt"><svg class="i"><use href="#i-plus"/></svg> Tambah Menu</button>
                </div>

                <div class="card">
                    <h3>Jenis Pembayaran</h3>
                    <div class="radio-list">
                        @foreach($paymentTypes as $p)
                            <label>
                                <input type="radio" name="payment_type_id" value="{{ $p->id }}"
                                       @checked((string) old('payment_type_id', $loop->first ? $p->id : '') === (string) $p->id) required>
                                {{ $p->name }}
                            </label>
                        @endforeach
                    </div>

                    <div class="total-box">
                        <span class="muted">Total</span>
                        <strong id="grandTotal">Rp 0</strong>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt" id="saveBtn">Simpan Transaksi</button>
                </div>
            </div>
        </form>
    @endif
@endsection

@push('scripts')
<script>
(function () {
    var rowsEl = document.getElementById('rows');
    if (!rowsEl) { return; }

    var MENUS = @json($menuList);
    var OLD = @json(array_values((array) old('items', [])));
    var totalEl = document.getElementById('grandTotal');
    var idx = 0;

    function fmt(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }
    function priceOf(id) {
        for (var k = 0; k < MENUS.length; k++) { if (String(MENUS[k].id) === String(id)) { return MENUS[k].price; } }
        return 0;
    }

    function addRow(menuId, qty) {
        var i = idx++;
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td data-label="Menu"></td>' +
            '<td data-label="Harga" class="price">Rp 0</td>' +
            '<td data-label="Jumlah"><div class="qty"><button type="button" class="dec">−</button><input type="number" min="1" max="9999" required><button type="button" class="inc">+</button></div></td>' +
            '<td data-label="Subtotal" class="sub right">Rp 0</td>' +
            '<td class="act right"><button type="button" class="icon-btn danger del" title="Hapus baris"><svg class="i"><use href="#i-trash"/></svg></button></td>';

        var sel = document.createElement('select');
        sel.name = 'items[' + i + '][menu_id]';
        sel.required = true;
        sel.add(new Option('Pilih menu…', ''));
        MENUS.forEach(function (m) { sel.add(new Option(m.name, m.id)); });
        tr.children[0].appendChild(sel);

        var qtyIn = tr.querySelector('input');
        qtyIn.name = 'items[' + i + '][qty]';
        qtyIn.value = qty || 1;
        if (menuId) { sel.value = menuId; }

        tr.querySelector('.dec').addEventListener('click', function () { qtyIn.value = Math.max(1, (parseInt(qtyIn.value, 10) || 1) - 1); recalc(); });
        tr.querySelector('.inc').addEventListener('click', function () { qtyIn.value = Math.min(9999, (parseInt(qtyIn.value, 10) || 0) + 1); recalc(); });
        tr.querySelector('.del').addEventListener('click', function () { tr.remove(); if (!rowsEl.children.length) { addRow(); } recalc(); });
        sel.addEventListener('change', recalc);
        qtyIn.addEventListener('input', recalc);

        rowsEl.appendChild(tr);
        recalc();
    }

    function recalc() {
        var total = 0, chosen = [];
        Array.prototype.forEach.call(rowsEl.querySelectorAll('tr'), function (tr) {
            var v = tr.querySelector('select').value;
            if (v) { chosen.push(v); }
        });
        Array.prototype.forEach.call(rowsEl.querySelectorAll('tr'), function (tr) {
            var sel = tr.querySelector('select');
            var qty = parseInt(tr.querySelector('input').value, 10) || 0;
            var p = priceOf(sel.value), sub = p * qty;
            tr.querySelector('.price').textContent = fmt(p);
            tr.querySelector('.sub').textContent = fmt(sub);
            total += sub;
            // menu yang sudah dipilih di baris lain tidak bisa dipilih lagi
            Array.prototype.forEach.call(sel.options, function (o) {
                o.disabled = o.value !== '' && o.value !== sel.value && chosen.indexOf(o.value) !== -1;
            });
        });
        totalEl.textContent = fmt(total);
    }

    document.getElementById('addRow').addEventListener('click', function () { addRow(); });
    document.getElementById('saleForm').addEventListener('submit', function () {
        document.getElementById('saveBtn').disabled = true; // cegah klik dobel
    });

    if (OLD.length) { OLD.forEach(function (o) { addRow(o.menu_id, o.qty); }); } else { addRow(); }
})();
</script>
@endpush
