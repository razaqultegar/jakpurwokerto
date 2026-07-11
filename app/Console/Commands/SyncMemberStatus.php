<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\MemberStatusLog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('members:sync-status')]
#[Description('Sinkronisasi status anggota kadaluwarsa berdasarkan masa berlaku KTA')]
class SyncMemberStatus extends Command
{
    public function handle(): int
    {
        $today = Carbon::today();

        $expired = Member::where('status', 'Aktif')
            ->whereNotNull('valid_until')
            ->whereDate('valid_until', '<', $today)
            ->get();

        $count = 0;
        foreach ($expired as $member) {
            $member->status = 'Tidak Aktif';
            $member->save();

            MemberStatusLog::create([
                'member_id'   => $member->id,
                'from_status' => 'Aktif',
                'to_status'   => 'Tidak Aktif',
                'reason'      => 'KTA kadaluwarsa otomatis (valid_until: '.$member->valid_until->format('Y-m-d').')',
            ]);
            $count++;
        }

        $this->info("Sinkronisasi selesai. {$count} anggota diubah menjadi Tidak Aktif.");

        return self::SUCCESS;
    }
}
