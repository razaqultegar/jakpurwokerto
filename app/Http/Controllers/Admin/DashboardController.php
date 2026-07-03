<?php

namespace App\Http\Controllers\Admin;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('pages.admin.dashboard', [
            'title' => 'Beranda',
            'stats' => $this->orderStats(),
            'stockCards' => $this->stockCards(),
            'filterCategory' => null,
        ]);
    }
}
