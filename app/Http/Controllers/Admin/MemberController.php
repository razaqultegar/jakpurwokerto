<?php

namespace App\Http\Controllers\Admin;

use App\Models\District;
use App\Models\Member;
use App\Models\MemberRegistration;
use App\Models\MemberStatusLog;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;

class MemberController extends Controller
{
    public function index()
    {
        return view('pages.admin.members.index', [
            'title'   => 'Anggota',
            'heading' => 'Manajemen Anggota',
        ]);
    }

    public function data(Request $request)
    {
        $draw      = (int) $request->input('draw', 1);
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $search    = trim((string) $request->input('search.value', ''));

        $columns = [
            0 => 'registration_number',
            1 => 'card_number',
            2 => 'name',
            3 => 'gender',
            4 => 'dob',
            5 => 'status',
            6 => 'valid_until',
        ];

        $orderColIdx = (int) $request->input('order.0.column', 2);
        $orderDir    = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'name';

        $base  = Member::query()->with(['province', 'regency', 'district']);
        $total = (clone $base)->count();

        if ($search !== '') {
            $like = '%'.$search.'%';
            $base->where(function ($q) use ($like) {
                $q->where('registration_number', 'like', $like)
                    ->orWhere('card_number', 'like', $like)
                    ->orWhere('nik', 'like', $like)
                    ->orWhere('name', 'like', $like)
                    ->orWhere('pob', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('address_street', 'like', $like);
            });
        }

        $filtered = (clone $base)->count();

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)
            ->take($length > 0 ? $length : 10)
            ->get();

        $data = $rows->map(fn (Member $m) => [
            'registration_number' => $m->registration_number ?? '-',
            'card_number'         => $m->card_number ?? '-',
            'name'                => $m->name ?? '-',
            'gender'              => $m->gender ?? '-',
            'pob'                 => $m->pob ?: '-',
            'dob'                 => $m->dob?->format('d M Y') ?: '-',
            'age'                 => $m->dob?->age,
            'address'             => $this->composeAddress($m),
            'status'              => $m->status ?? '-',
            'valid_until'         => $m->valid_until?->format('d M Y') ?: '-',
        ]);

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        $uploaded = $request->file('file');
        $tempPath = $uploaded->getRealPath();

        try {
            $spreadsheet = IOFactory::load($tempPath);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'File Excel tidak dapat dibaca. Pastikan format sesuai template.',
            ], 422);
        }

        $sheet = $spreadsheet->getSheetByName('MASTER DATA')
            ?? $spreadsheet->getActiveSheet();

        $rows    = $sheet->toArray(null, true, true, false);

        // Cari baris data pertama yang memiliki NAMA LENGKAP (kolom H = idx 7)
        $firstDataRow = null;
        for ($i = 2; $i < count($rows); $i++) {
            if (trim((string) ($rows[$i][7] ?? '')) !== '') {
                $firstDataRow = $i;
                break;
            }
        }

        if ($firstDataRow === null) {
            return response()->json([
                'message' => 'Data tidak ditemukan pada sheet MASTER DATA. Gunakan template yang disediakan.',
            ], 422);
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        // Mulai dari baris ke-3 (index 2) — lewati 2 baris header bertingkat
        for ($i = 2; $i < count($rows); $i++) {
            $row = $rows[$i];

            $name = trim((string) $this->cellString($row, 7));   // H: NAMA LENGKAP
            if ($name === '') {
                $skipped++;
                continue;
            }

            try {
                $this->upsertMemberFromRow($row);
                $imported++;
            } catch (\Throwable $e) {
                $skipped++;
                $errors[] = "Baris ".($i + 1).": ".$e->getMessage();
            }
        }

        return response()->json([
            'message'  => "Impor selesai. {$imported} anggota diproses, {$skipped} dilewati.",
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ]);
    }

    public function downloadTemplate()
    {
        $path = 'templates/sample_members.xlsx';

        if (! Storage::disk('public')->exists($path)) {
            abort(404, 'Template tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $path,
            'sample_members.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    /**
     * Mapping kolom berdasarkan header bertingkat di sample_members.xlsx:
     * A(0): TIPE, B(1): SEKTOR, C(2): NO. REGISTRASI, D(3): NO. KTA,
     * E(4): MASA AKTIF MULAI, F(5): SAMPAI, G(6): NIK, H(7): NAMA LENGKAP,
     * I(8): TEMPAT LAHIR, J(9): TANGGAL LAHIR, K(10): JENIS KELAMIN,
     * L(11): GOL. DARAH, M(12): ALAMAT JALAN, N(13): DESA/KELURAHAN,
     * O(14): KECAMATAN, P(15): KABUPATEN/KOTA, Q(16): PROVINSI,
     * R(17): NO. TELEPON/WHATSAPP, S(18): ALAMAT EMAIL,
     * T(19): UKURAN KAOS, U(20): STATUS, V(21): WAKTU REGISTRASI.
     */
    private function upsertMemberFromRow(array $row): Member
    {
        $registrationNumber = $this->cellString($row, 2);
        $cardNumber         = $this->cellString($row, 3);
        $name               = $this->cellString($row, 7);
        $nik                = $this->cellString($row, 6);
        $pob                = $this->cellString($row, 8);
        $dob                = $this->cellDate($row, 9);
        $gender             = $this->cellString($row, 10);
        $bloodType          = $this->cellString($row, 11);
        $addressStreet      = $this->cellString($row, 12);
        $phone              = $this->cellString($row, 17);
        $email              = $this->cellString($row, 18);
        $shirtSize          = $this->cellString($row, 19);
        $status             = $this->cellString($row, 20) ?: 'Dalam Proses';
        $registeredAt       = $this->cellDate($row, 21, true);
        $validFrom          = $this->cellDate($row, 4);
        $validUntil         = $this->cellDate($row, 5);

        $type   = $this->cellString($row, 0) ?: 'Baru';
        $sector = $this->cellString($row, 1);

        // Wilayah lookup (berdasarkan nama) — simpan sebagai FK bila ditemukan
        $districtName = $this->cellString($row, 14);
        $regencyName  = $this->cellString($row, 15);
        $provinceName = $this->cellString($row, 16);

        $regionIds = $this->resolveRegionIds($provinceName, $regencyName, $districtName);

        $identifier = array_filter([
            $registrationNumber,
            $cardNumber,
            $nik,
        ]);

        $member = null;
        foreach ($identifier as $value) {
            $match = Member::query()
                ->where('registration_number', $value)
                ->orWhere('card_number', $value)
                ->orWhere('nik', $value)
                ->first();
            if ($match) {
                $member = $match;
                break;
            }
        }

        $data = array_filter([
            'registration_number' => $registrationNumber,
            'card_number'         => $cardNumber,
            'nik'                 => $nik,
            'name'                => $name,
            'pob'                 => $pob,
            'dob'                 => $dob,
            'gender'              => $gender,
            'blood_type'          => $bloodType,
            'shirt_size'          => $shirtSize,
            'address_street'      => $addressStreet,
            'district_id'         => $regionIds['district_id'] ?? null,
            'regency_id'          => $regionIds['regency_id'] ?? null,
            'province_id'         => $regionIds['province_id'] ?? null,
            'phone'               => $phone,
            'email'               => $email,
            'status'              => $status,
            'valid_from'          => $validFrom,
            'valid_until'         => $validUntil,
            'registered_at'       => $registeredAt ?? Carbon::now(),
        ], fn ($v) => $v !== null && $v !== '');

        if ($member) {
            $prevStatus = $member->status;
            $member->update($data);

            // Catat log bila status berubah pada re-impor
            if ($prevStatus !== $status) {
                MemberStatusLog::create([
                    'member_id'   => $member->id,
                    'from_status' => $prevStatus,
                    'to_status'   => $status,
                    'reason'      => 'Perubahan saat impor ulang',
                ]);
            }
        } else {
            $member = Member::create($data);
            MemberStatusLog::create([
                'member_id'   => $member->id,
                'from_status' => null,
                'to_status'   => $status,
                'reason'      => 'Impor awal via template',
            ]);
        }

        // Hindari duplikasi log pendaftaran pada re-impor baris yang sama.
        // Hanya tambahkan entri baru bila ada perubahan (tipe/sektor/masa berlaku).
        $lastReg = MemberRegistration::query()
            ->where('member_id', $member->id)
            ->latest('id')
            ->first();

        $regChanged = ! $lastReg
            || $lastReg->registration_type !== $type
            || ($lastReg->sector ?? '') !== $sector
            || (($lastReg->valid_from?->format('Y-m-d') ?? '') !== ($validFrom ?? ''))
            || (($lastReg->valid_until?->format('Y-m-d') ?? '') !== ($validUntil ?? ''));

        if ($regChanged) {
            MemberRegistration::create([
                'member_id'         => $member->id,
                'registration_type' => $type,
                'sector'            => $sector,
                'registered_at'     => $registeredAt ?? Carbon::now(),
                'valid_from'        => $validFrom,
                'valid_until'       => $validUntil,
            ]);
        }

        return $member;
    }

    private function composeAddress(Member $m): string
    {
        $parts = array_filter([
            $m->address_street,
            $m->district?->name,
            $m->regency?->name,
            $m->province?->name,
        ]);

        return implode(', ', $parts) ?: '-';
    }

    /**
     * Cari ID wilayah (province/regency/district) berdasarkan nama.
     * Mencocakkan secara case-insensitive dan trim spasi.
     */
    private function resolveRegionIds(string $provinceName, string $regencyName, string $districtName): array
    {
        $result = [];

        $province = $this->regionMatch(Province::query(), $provinceName);
        if ($province) {
            $result['province_id'] = $province->id;

            $regency = $this->regionMatch(
                Regency::query()->where('province_id', $province->id),
                $regencyName
            );
            if ($regency) {
                $result['regency_id'] = $regency->id;

                $district = $this->regionMatch(
                    District::query()->where('regency_id', $regency->id),
                    $districtName
                );
                if ($district) {
                    $result['district_id'] = $district->id;
                }
            }
        }

        return $result;
    }

    private function regionMatch($query, string $name)
    {
        if ($name === '') {
            return null;
        }

        return $query->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
    }

    private function cellString(array $row, int $idx): string
    {
        $value = $row[$idx] ?? null;

        return trim((string) $value);
    }

    private function cellDate(array $row, int $idx, bool $withTime = false): ?string
    {
        $value = $row[$idx] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                $date = SpreadsheetDate::excelToDateTimeObject($value);
            } else {
                $date = new \DateTime($value);
            }

            return $withTime
                ? $date->format('Y-m-d H:i:s')
                : $date->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
