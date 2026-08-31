<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;

class TicketController extends Controller
{
    public function __invoke()
    {
        return view('pages.admin.ticket.index', [
            'title' => 'Pesanan Tiket',
            'stats' => $this->ticketStats(),
            'stockCards' => [],
            'filterCategory' => 'Tiket',
            'orderScope' => 'ticket',
            'ordersOpen' => Setting::bool('ticket_orders_open', true),
        ]);
    }
}
