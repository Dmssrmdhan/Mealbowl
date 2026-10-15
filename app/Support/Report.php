<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Semua laporan (harian/mingguan/bulanan/tahunan) dihitung langsung dari
 * tabel transactions & transaction_details, jadi selalu akurat dan tidak
 * perlu tabel laporan terpisah.
 */
class Report
{
    /** Nama kolom yang sudah di-quote sesuai database (backtick MySQL / "..." PostgreSQL) */
    private static function col(string $name): string
    {
        return DB::getQueryGrammar()->wrap($name);
    }

    /** Ekspresi SQL "nomor bulan (1-12)" dari kolom tanggal, beda sintaks MySQL vs PostgreSQL */
    private static function monthExpr(string $column): string
    {
        $c = self::col($column);

        return DB::connection()->getDriverName() === 'pgsql'
            ? "CAST(EXTRACT(MONTH FROM {$c}) AS INTEGER)"
            : "MONTH({$c})";
    }

    public static function summary(Carbon $from, Carbon $to): array
    {
        $f = $from->toDateString();
        $t = $to->toDateString();

        $tx = DB::table('transactions')->whereBetween('date', [$f, $t]);
        $total = (int) (clone $tx)->sum('total');
        $orders = (int) (clone $tx)->count();

        $byMenu = DB::table('transaction_details as d')
            ->join('transactions as t', 't.id', '=', 'd.transaction_id')
            ->join('menus as m', 'm.id', '=', 'd.menu_id')
            ->whereBetween('t.date', [$f, $t])
            ->groupBy('m.id', 'm.name')
            ->selectRaw('m.name as name, SUM(d.qty) as qty, SUM(d.subtotal) as total')
            ->orderByDesc('qty')
            ->orderBy('m.name')
            ->get();

        $pcs = (int) $byMenu->sum('qty');

        $byPayment = DB::table('transactions as t')
            ->join('payment_types as p', 'p.id', '=', 't.payment_type_id')
            ->whereBetween('t.date', [$f, $t])
            ->groupBy('p.id', 'p.name')
            ->selectRaw('p.name as name, COUNT(*) as orders, SUM(t.total) as total')
            ->orderByDesc('total')
            ->get();

        return compact('total', 'orders', 'pcs', 'byMenu', 'byPayment');
    }

    /**
     * Pendapatan per hari. $labelMode: 'day' (15), 'dm' (15 Okt), 'dow' (Kam)
     */
    public static function dailySeries(Carbon $from, Carbon $to, string $labelMode = 'day'): array
    {
        $rows = DB::table('transactions')
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('date')
            ->selectRaw(self::col('date') . ' as k, SUM(total) as total, COUNT(*) as orders')
            ->get()
            ->keyBy('k');

        $out = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $r = $rows->get($d->toDateString());

            if ($labelMode === 'dm') {
                $label = $d->day . ' ' . Fmt::MONTHS_SHORT[$d->month];
            } elseif ($labelMode === 'dow') {
                $label = substr(Fmt::DAYS[$d->dayOfWeekIso], 0, 3);
            } else {
                $label = (string) $d->day;
            }

            $out[] = [
                'label' => $label,
                'full' => Fmt::tanggal($d),
                'total' => $r ? (int) $r->total : 0,
                'orders' => $r ? (int) $r->orders : 0,
            ];
        }

        return $out;
    }

    /** Pendapatan per bulan dalam satu tahun */
    public static function monthlySeries(int $year): array
    {
        $rows = DB::table('transactions')
            ->whereBetween('date', [$year . '-01-01', $year . '-12-31'])
            ->groupBy(DB::raw(self::monthExpr('date')))
            ->selectRaw(self::monthExpr('date') . ' as k, SUM(total) as total, COUNT(*) as orders')
            ->get()
            ->keyBy('k');

        $out = [];
        for ($m = 1; $m <= 12; $m++) {
            $r = $rows->get($m);
            $out[] = [
                'label' => Fmt::MONTHS_SHORT[$m],
                'full' => Fmt::MONTHS[$m] . ' ' . $year,
                'total' => $r ? (int) $r->total : 0,
                'orders' => $r ? (int) $r->orders : 0,
            ];
        }

        return $out;
    }
}
