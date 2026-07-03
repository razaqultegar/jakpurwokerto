<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function index()
    {
        return view('pages.admin.checkin.index', [
            'title' => 'Check-in Tiket',
        ]);
    }

    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20'],
        ]);

        $code = strtoupper(trim($validated['code']));

        return redirect()->route('admin.checkin.show', ['code' => $code]);
    }

    public function show(string $code)
    {
        $code = strtoupper(trim($code));
        $order = Order::where('checkin_code', $code)->first();

        return view('pages.admin.checkin.show', [
            'title' => 'Check-in Tiket',
            'code' => $code,
            'order' => $order,
        ]);
    }

    public function confirm(Request $request, string $code)
    {
        $code = strtoupper(trim($code));
        $order = Order::where('checkin_code', $code)->first();

        if (! $order) {
            return redirect()->route('admin.checkin.show', ['code' => $code])
                ->with('checkin_status', 'error')
                ->with('checkin_message', 'Kode tiket tidak ditemukan.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('admin.checkin.show', ['code' => $code])
                ->with('checkin_status', 'error')
                ->with('checkin_message', 'Pesanan ini sudah dibatalkan, tiket tidak berlaku.');
        }

        if ($order->checked_in_at) {
            return redirect()->route('admin.checkin.show', ['code' => $code])
                ->with('checkin_status', 'already')
                ->with('checkin_message', 'Tiket ini sudah pernah check-in sebelumnya.');
        }

        $order->update(['checked_in_at' => now()]);

        return redirect()->route('admin.checkin.show', ['code' => $code])
            ->with('checkin_status', 'success')
            ->with('checkin_message', 'Check-in berhasil dikonfirmasi.');
    }

    public function undo(string $code)
    {
        $code = strtoupper(trim($code));
        $order = Order::where('checkin_code', $code)->first();

        if ($order && $order->checked_in_at) {
            $order->update(['checked_in_at' => null]);
        }

        return redirect()->route('admin.checkin.show', ['code' => $code])
            ->with('checkin_status', 'undone')
            ->with('checkin_message', 'Status check-in dibatalkan.');
    }
}
