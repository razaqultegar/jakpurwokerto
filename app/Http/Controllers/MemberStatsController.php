<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MemberStatsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $totalMembers = Member::count();

        // Status distribution
        $statusCounts = Member::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Gender distribution
        $genderCounts = Member::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get()
            ->pluck('total', 'gender');

        // Sector distribution from latest registrations
        $sectorCounts = MemberRegistration::select('sector', DB::raw('count(*) as total'))
            ->whereNotNull('sector')
            ->where('sector', '!=', '')
            ->groupBy('sector')
            ->orderByDesc('total')
            ->get()
            ->pluck('total', 'sector');

        // Monthly registrations (last 6 months)
        $monthlyRegistrations = MemberRegistration::select(
            DB::raw("DATE_FORMAT(registered_at, '%Y-%m') as month"),
            DB::raw('count(*) as total')
        )
            ->whereNotNull('registered_at')
            ->where('registered_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        // Fill missing months with 0
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = Carbon::now()->subMonths($i)->format('Y-m');
            $label = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $months[] = [
                'label' => $label,
                'total' => $monthlyRegistrations->get($key, 0),
            ];
        }

        // Registration type distribution
        $typeCounts = MemberRegistration::select('registration_type', DB::raw('count(*) as total'))
            ->groupBy('registration_type')
            ->get()
            ->pluck('total', 'registration_type');

        return response()->json([
            'total' => $totalMembers,
            'status' => $statusCounts,
            'gender' => $genderCounts,
            'sector' => $sectorCounts,
            'monthly' => $months,
            'types' => $typeCounts,
        ]);
    }
}
