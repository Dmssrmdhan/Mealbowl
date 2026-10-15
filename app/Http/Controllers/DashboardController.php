<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use App\Support\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();

        $todaySummary = Report::summary($today, $today);
        $yesterdayIncome = (int) DB::table('transactions')->where('date', $yesterday->toDateString())->sum('total');

        $pct = $yesterdayIncome > 0
            ? (int) round(($todaySummary['total'] - $yesterdayIncome) / $yesterdayIncome * 100)
            : null;

        $monthSummary = Report::summary($today->copy()->startOfMonth(), $today->copy()->endOfMonth());

        return view('dashboard', [
            'income' => $todaySummary['total'],
            'orders' => $todaySummary['orders'],
            'pcs' => $todaySummary['pcs'],
            'pct' => $pct,
            'paymentTypeCount' => PaymentType::count(),
            'series' => Report::dailySeries($today->copy()->subDays(6), $today, 'dm'),
            'topMenus' => $monthSummary['byMenu']->take(5),
        ]);
    }
}
