<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Zona waktu Indonesia (WIB) supaya "hari ini" sesuai jam toko
        config(['app.timezone' => 'Asia/Jakarta']);
        date_default_timezone_set('Asia/Jakarta');
    }

    public function boot()
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Paginator::defaultView('vendor.pagination.mealbowl');
        Paginator::defaultSimpleView('vendor.pagination.mealbowl');

        // @rp($angka)  -> Rp 15.000
        Blade::directive('rp', function ($expression) {
            return "<?php echo \\App\\Support\\Fmt::rp($expression); ?>";
        });
        // @tgl($tanggal) -> 15 Okt 2026
        Blade::directive('tgl', function ($expression) {
            return "<?php echo \\App\\Support\\Fmt::tanggal($expression); ?>";
        });
        // @hari($tanggal) -> Kamis, 15 Oktober 2026
        Blade::directive('hari', function ($expression) {
            return "<?php echo \\App\\Support\\Fmt::hariTanggal($expression); ?>";
        });
    }
}
