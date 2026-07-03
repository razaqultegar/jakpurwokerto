<?php

namespace App\Http\Controllers\Admin;

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
}
