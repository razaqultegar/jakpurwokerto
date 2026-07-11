<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberRegistration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home.index', [
            'title' => 'Beranda',
            'articles' => ArticleController::latest(3),
        ]);
    }

    public function stats()
    {
        return view('pages.home.stats', [
            'title' => 'Statistik Keanggotaan',
            'memberStats' => $this->memberStats(),
        ]);
    }

    private function memberStats(): array
    {
        $total = Member::count();
        if ($total === 0) {
            return ['total' => 0];
        }

        $status = Member::select('status')
            ->whereNotNull('status')
            ->pluck('status')
            ->countBy()
            ->toArray();

        $gender = Member::select('gender')
            ->whereNotNull('gender')
            ->pluck('gender')
            ->countBy()
            ->toArray();

        $sector = MemberRegistration::select('sector')
            ->whereNotNull('sector')
            ->pluck('sector')
            ->countBy()
            ->toArray();

        $monthly = MemberRegistration::select(
                DB::raw("DATE_FORMAT(registered_at, '%b %Y') as label"),
                DB::raw('COUNT(*) as total')
            )
            ->where('registered_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('label')
            ->orderByRaw('MIN(registered_at)')
            ->get()
            ->toArray();

        // Age categories
        $now = Carbon::now();
        $ageCategories = [
            'Dewasa Muda (18-25)' => 0,
            'Dewasa (26-35)' => 0,
            'Dewasa Senior (36-50)' => 0,
            'Lansia (51+)' => 0,
            'Tidak Diketahui' => 0,
        ];

        $members = Member::select('dob')->get();
        foreach ($members as $m) {
            if (!$m->dob) {
                $ageCategories['Tidak Diketahui']++;
                continue;
            }
            $age = $m->dob->age;
            if ($age < 18) {
                $ageCategories['Dewasa Muda (18-25)']++;
            } elseif ($age <= 25) {
                $ageCategories['Dewasa Muda (18-25)']++;
            } elseif ($age <= 35) {
                $ageCategories['Dewasa (26-35)']++;
            } elseif ($age <= 50) {
                $ageCategories['Dewasa Senior (36-50)']++;
            } else {
                $ageCategories['Lansia (51+)']++;
            }
        }

        return compact('total', 'status', 'gender', 'sector', 'monthly', 'ageCategories');
    }
}
