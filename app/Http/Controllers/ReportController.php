<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Support\Fmt;
use App\Support\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'daily');
        if (!in_array($type, ['daily', 'weekly', 'monthly', 'yearly'], true)) {
            $type = 'daily';
        }

        try {
            $date = Carbon::parse($request->query('date', Carbon::today()->toDateString()))->startOfDay();
        } catch (\Throwable $e) {
            $date = Carbon::today();
        }

        switch ($type) {
            case 'weekly':
                $from = $date->copy()->startOfWeek(Carbon::MONDAY);
                $to = $date->copy()->endOfWeek(Carbon::SUNDAY)->startOfDay();
                $label = Fmt::tanggal($from) . ' – ' . Fmt::tanggal($to);
                $series = Report::dailySeries($from, $to, 'dow');
                break;
            case 'monthly':
                $from = $date->copy()->startOfMonth();
                $to = $date->copy()->endOfMonth()->startOfDay();
                $label = Fmt::bulanTahun($date);
                $series = Report::dailySeries($from, $to, 'day');
                break;
            case 'yearly':
                $from = $date->copy()->startOfYear();
                $to = $date->copy()->endOfYear()->startOfDay();
                $label = (string) $date->year;
                $series = Report::monthlySeries($date->year);
                break;
            default: // daily
                $from = $date->copy();
                $to = $date->copy();
                $label = Fmt::hariTanggal($date);
                $series = null;
        }

        $transactions = null;
        if ($type === 'daily') {
            $transactions = Transaction::with(['paymentType', 'details.menu'])
                ->whereDate('date', $date->toDateString())
                ->orderBy('id')
                ->get();
        }

        return view('reports.index', [
            'type' => $type,
            'date' => $date,
            'label' => $label,
            'summary' => Report::summary($from, $to),
            'series' => $series,
            'transactions' => $transactions,
        ]);
    }
}
