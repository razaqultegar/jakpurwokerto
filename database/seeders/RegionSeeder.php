<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $file = storage_path('app/private/seeders/regions.xlsx');

        if (! file_exists($file)) {
            $this->command->error("File regions.xlsx tidak ditemukan di: {$file}");

            return;
        }

        $reader = new XlsxReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);

        // Provinsi: [id, name]
        $provRows = $spreadsheet->getSheetByName('Provinsi')->toArray(null, true, true, false);
        $provinceData = [];
        foreach ($provRows as $row) {
            $id = isset($row[0]) ? (int) $row[0] : null;
            $name = isset($row[1]) ? trim((string) $row[1]) : null;
            if ($id && $name !== '') {
                $provinceData[] = ['id' => $id, 'name' => $name];
            }
        }
        Province::upsert($provinceData, ['id'], ['name']);
        $this->command->info('Provinsi: '.count($provinceData).' baris.');

        // Kabupaten: [id, province_id, name]
        $kabRows = $spreadsheet->getSheetByName('Kabupaten')->toArray(null, true, true, false);
        $regencyData = [];
        foreach ($kabRows as $row) {
            $id = isset($row[0]) ? (int) $row[0] : null;
            $provinceId = isset($row[1]) ? (int) $row[1] : null;
            $name = isset($row[2]) ? trim((string) $row[2]) : null;
            if ($id && $provinceId && $name !== '') {
                $regencyData[] = ['id' => $id, 'province_id' => $provinceId, 'name' => $name];
            }
        }
        Regency::upsert($regencyData, ['id'], ['province_id', 'name']);
        $this->command->info('Kabupaten: '.count($regencyData).' baris.');

        // Kecamatan: [id, regency_id, name]
        $kecRows = $spreadsheet->getSheetByName('Kecamatan')->toArray(null, true, true, false);
        $districtData = [];
        foreach ($kecRows as $row) {
            $id = isset($row[0]) ? (int) $row[0] : null;
            $regencyId = isset($row[1]) ? (int) $row[1] : null;
            $name = isset($row[2]) ? trim((string) $row[2]) : null;
            if ($id && $regencyId && $name !== '') {
                $districtData[] = ['id' => $id, 'regency_id' => $regencyId, 'name' => $name];
            }
        }
        // Chunk untuk efisiensi memori
        foreach (array_chunk($districtData, 1000) as $chunk) {
            District::upsert($chunk, ['id'], ['regency_id', 'name']);
        }
        $this->command->info('Kecamatan: '.count($districtData).' baris.');
    }
}
