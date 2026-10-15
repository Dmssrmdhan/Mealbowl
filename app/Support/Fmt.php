<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Format angka & tanggal berbahasa Indonesia (tanpa perlu ubah config locale).
 */
class Fmt
{
    public const MONTHS = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    public const MONTHS_SHORT = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    public const DAYS = [1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public static function rp($n): string
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }

    /** 15 Okt 2026 (atau 15 Oktober 2026 bila $short = false) */
    public static function tanggal($date, bool $short = true): string
    {
        $d = Carbon::parse($date);
        $month = $short ? self::MONTHS_SHORT[$d->month] : self::MONTHS[$d->month];

        return $d->day . ' ' . $month . ' ' . $d->year;
    }

    /** Kamis, 15 Oktober 2026 */
    public static function hariTanggal($date): string
    {
        $d = Carbon::parse($date);

        return self::DAYS[$d->dayOfWeekIso] . ', ' . self::tanggal($d, false);
    }

    /** Oktober 2026 */
    public static function bulanTahun($date): string
    {
        $d = Carbon::parse($date);

        return self::MONTHS[$d->month] . ' ' . $d->year;
    }

    /** 1,2jt / 350rb / 900 -- untuk label grafik */
    public static function ringkas($n): string
    {
        $n = (float) $n;
        if ($n >= 1000000) {
            return rtrim(rtrim(number_format($n / 1000000, 1, ',', '.'), '0'), ',') . 'jt';
        }
        if ($n >= 1000) {
            return round($n / 1000) . 'rb';
        }

        return (string) round($n);
    }
}
