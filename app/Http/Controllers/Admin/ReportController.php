<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $orders = Order::query()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->with(['customer', 'store'])
            ->latest()
            ->get();

        $filename = "repomart-orders-{$from}-to-{$to}.csv";

        return response()->streamDownload(function () use ($orders): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Date', 'Customer', 'Store', 'Status', 'Subtotal', 'Delivery Fee', 'Total']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->toDateTimeString(),
                    $order->customer?->name,
                    $order->store?->store_name,
                    $order->status->label(),
                    $order->subtotal,
                    $order->delivery_fee,
                    $order->total,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
