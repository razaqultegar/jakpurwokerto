<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;

class MerchandiseController extends Controller
{
    public function __invoke()
    {
        return view('pages.admin.merchandise.index', [
            'title' => 'Pesanan Merchandise',
            'stats' => $this->merchandiseStats(),
            'stockCards' => $this->stockCards(),
            'filterCategory' => 'Merchandise',
            'orderScope' => 'merchandise',
            'ordersOpen' => Setting::bool('merchandise_orders_open', true),
        ]);
    }
}
