<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CheckinScanController extends Controller
{
    private const DEVICE_COOKIE_MINUTES = 60 * 24 * 30;

    public function pinForm()
    {
        if (session('checkin_pin_verified')) {
            return redirect()->route('checkin.index');
        }

        return view('pages.checkin.pin', [
            'title' => 'Akses Petugas Check-in',
        ]);
    }

    public function verifyPin(Request $request)
    {
        $validated = $request->validate([
            'pin' => ['required', 'string'],
        ]);

        if ($validated['pin'] !== config('checkin.pin')) {
            return back()->withErrors(['pin' => 'PIN salah. Coba lagi.']);
        }

        $request->session()->put('checkin_pin_verified', true);
        $intended = $request->session()->pull('checkin_intended_url');

        $cookie = Cookie::make('checkin_device', hash('sha256', config('checkin.pin')), self::DEVICE_COOKIE_MINUTES);

        return redirect($intended ?: route('checkin.index'))->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('checkin_pin_verified');

        return redirect()->route('checkin.pin')->withCookie(Cookie::forget('checkin_device'));
    }

    public function index()
    {
        return view('pages.checkin.scan', [
            'title' => 'Scan Tiket',
        ]);
    }

    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100'],
        ]);

        $code = strtoupper(trim(basename(parse_url($validated['code'], PHP_URL_PATH) ?: $validated['code'])));

        return $this->ticketResponse($code);
    }

    public function confirm(string $code)
    {
        $code = strtoupper(trim($code));
        $order = Order::where('checkin_code', $code)->first();

        if (! $order || $order->status === 'cancelled' || $order->checked_in_at) {
            return $this->ticketResponse($code);
        }

        $order->update(['checked_in_at' => now()]);

        return $this->ticketResponse($code);
    }

    public function undo(string $code)
    {
        $code = strtoupper(trim($code));
        $order = Order::where('checkin_code', $code)->first();

        if ($order && $order->checked_in_at) {
            $order->update(['checked_in_at' => null]);
        }

        return $this->ticketResponse($code);
    }

    private function ticketResponse(string $code)
    {
        $order = Order::where('checkin_code', $code)->first();

        if (! $order) {
            return response()->json([
                'ok' => true,
                'status' => 'not_found',
                'message' => 'Kode tiket tidak ditemukan.',
                'code' => $code,
            ]);
        }

        $status = 'valid';
        $message = 'Tiket valid — belum check-in.';

        if ($order->status === 'cancelled') {
            $status = 'cancelled';
            $message = 'Pesanan ini sudah dibatalkan, tiket tidak berlaku.';
        } elseif ($order->checked_in_at) {
            $status = 'checked_in';
            $message = 'Tiket ini sudah check-in.';
        }

        return response()->json([
            'ok' => true,
            'status' => $status,
            'message' => $message,
            'code' => $code,
            'order' => [
                'order_id' => $order->order_id,
                'customer_name' => $order->customer_name,
                'items_label' => collect($order->item ?? [])->pluck('name')->filter()->implode(', '),
                'items_qty' => collect($order->item ?? [])->sum('qty'),
                'checked_in_at' => $order->checked_in_at
                    ? $order->checked_in_at->locale('id')->translatedFormat('d F Y, H:i').' WIB'
                    : null,
                'can_confirm' => $status === 'valid',
                'can_undo' => $status === 'checked_in',
            ],
        ]);
    }
}
