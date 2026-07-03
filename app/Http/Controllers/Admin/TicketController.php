<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;

class TicketController extends Controller
{
    public function __invoke()
    {
        return view('pages.admin.ticket.index', [
            'title' => 'Pesanan Tiket',
            'stats' => $this->ticketStats(),
            'stockCards' => [],
            'filterCategory' => 'Tiket',
        ]);
    }

    private function ticketStats(): array
    {
        $base = Order::whereRaw("JSON_SEARCH(item, 'one', 'Tiket', NULL, '$[*].category') IS NOT NULL");
        $confirmed = ['verified', 'paid', 'shipped', 'completed'];

        return [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'verified' => (clone $base)->where('status', 'verified')->count(),
            'paid' => (clone $base)->where('status', 'paid')->count(),
            'shipped' => (clone $base)->where('status', 'shipped')->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
            'confirmed' => (clone $base)->whereIn('status', $confirmed)->count(),
            'settled' => (clone $base)->whereIn('status', ['paid', 'shipped', 'completed'])->count(),
            'revenue' => (clone $base)->whereIn('status', $confirmed)->sum('amount_due'),
        ];
    }
}
