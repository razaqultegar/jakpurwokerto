<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private const ORDER_TOGGLES = [
        'ticket' => [
            'key' => 'ticket_orders_open',
            'label' => 'Pemesanan tiket',
        ],
        'merchandise' => [
            'key' => 'merchandise_orders_open',
            'label' => 'Pemesanan merchandise',
        ],
    ];

    public function toggleOrders(Request $request)
    {
        $validated = $request->validate([
            'scope' => ['required', 'string', 'in:'.implode(',', array_keys(self::ORDER_TOGGLES))],
            'open' => ['required', 'boolean'],
        ]);

        $toggle = self::ORDER_TOGGLES[$validated['scope']];
        $open = (bool) $validated['open'];

        Setting::putBool($toggle['key'], $open);

        return back()->with('status', sprintf(
            '%s sekarang %s.',
            $toggle['label'],
            $open ? 'DIBUKA' : 'DITUTUP'
        ));
    }
}
