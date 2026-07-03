<?php

namespace App\Http\Controllers\Admin;

class MerchandiseController extends Controller
{
    public function __invoke()
    {
        return view('pages.admin.merchandise.index', [
            'title' => 'Pesanan Merchandise',
            'stats' => $this->merchandiseStats(),
            'stockCards' => $this->stockCards(),
            'filterCategory' => 'Merchandise',
        ]);
    }
}
