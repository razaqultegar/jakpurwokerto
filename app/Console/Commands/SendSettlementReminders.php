<?php

namespace App\Console\Commands;

use App\Mail\OrderInvoice;
use App\Models\Order;
use App\Support\OrderPresenter;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSettlementReminders extends Command
{
    protected $signature = 'orders:settlement-reminders
        {--date= : Anggap hari ini sebagai tanggal ini (YYYY-MM-DD), untuk pengujian}
        {--force : Kirim ulang walau tahap pengingat sudah pernah terkirim}';

    protected $description = 'Kirim email pengingat pelunasan DP pada H-7, H-5, H-3, dan Hari-H sebelum tenggat.';

    public function handle(): int
    {
        $deadline = Carbon::parse(config('settlement.deadline'))->startOfDay();
        $today = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::now()->startOfDay();

        $offsets = config('settlement.reminder_offsets', [7, 5, 3, 0]);

        // Tentukan tahap pengingat yang jatuh tepat hari ini.
        $offset = null;
        foreach ($offsets as $o) {
            if ($deadline->copy()->subDays($o)->isSameDay($today)) {
                $offset = $o;
                break;
            }
        }

        if ($offset === null) {
            $this->info('Tidak ada jadwal pengingat pelunasan untuk hari ini ('.$today->toDateString().').');

            return self::SUCCESS;
        }

        $stageKey = 'h-'.$offset;
        $stageLabel = $offset === 0 ? 'Hari H' : 'H-'.$offset;
        $deadlineLabel = $deadline->copy()->locale('id')->translatedFormat('d F Y');

        $this->info("Pengingat pelunasan tahap {$stageLabel} (tenggat {$deadlineLabel}).");

        // Pesanan DP yang DP-nya sudah diverifikasi tetapi belum lunas & masih ada sisa.
        $orders = Order::query()
            ->where('payment_type', 'dp')
            ->where('status', 'verified')
            ->whereNull('dp_settlement_verified_at')
            ->whereColumn('amount_due', '<', 'subtotal')
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($orders as $order) {
            $reminders = $order->dp_settlement_reminders ?? [];

            if (! $this->option('force') && in_array($stageKey, $reminders, true)) {
                $skipped++;

                continue;
            }

            $data = OrderPresenter::mailData($order);
            $data['reminder'] = [
                'stage' => $stageLabel,
                'deadline_label' => $deadlineLabel,
                'is_due' => $offset === 0,
            ];

            try {
                Mail::to($order->customer_email)->send(new OrderInvoice($data, 'reminder'));

                if (! in_array($stageKey, $reminders, true)) {
                    $reminders[] = $stageKey;
                }
                $order->update(['dp_settlement_reminders' => $reminders]);
                $sent++;
                $this->line("  ✓ {$order->order_id} → {$order->customer_email}");
            } catch (\Throwable $e) {
                Log::error('Failed to send settlement reminder', [
                    'order_id' => $order->order_id,
                    'stage' => $stageKey,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  ✗ {$order->order_id}: {$e->getMessage()}");
            }
        }

        $this->info("Selesai. Terkirim: {$sent}, dilewati (sudah pernah): {$skipped}.");

        return self::SUCCESS;
    }
}
