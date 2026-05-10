<?php

namespace App\Modules\Logistics\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Services\PerformanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    use ApiResponse;

    public function __construct(private PerformanceService $performanceService)
    {
    }

    /**
     * Download a PDF performance report for a driver.
     */
    public function downloadDriverPerformance(string $driverId)
    {
        $driver = Driver::with('user')->findOrFail($driverId);
        $stats = $this->performanceService->getWeeklyDriverStats($driverId);

        $pdf = Pdf::loadView('reports.driver_performance', [
            'driver' => $driver,
            'stats'  => $stats
        ]);

        return $pdf->download("performance_{$driver->user->name}_" . now()->format('Y-m-d') . ".pdf");
    }

    /**
     * Export recently paid logistics transactions as a CSV.
     */
    public function exportFinancials()
    {
        $fileName = 'financials_' . now()->format('Y-m-d') . '.csv';
        $orders = \App\Modules\Logistics\Models\Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subMonth())
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order ID', 'Reference', 'Total Amount', 'Currency', 'Status', 'Date']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->reference,
                    $order->total_amount,
                    $order->currency,
                    $order->status,
                    $order->created_at->toDateTimeString(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
