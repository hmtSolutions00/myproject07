<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

use App\Models\Menteri;
use App\Models\DetailMenteri;

class DetailMenteriSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/detail_menteri.json');

        if (!File::exists($path)) {
            $this->command->error("File JSON tidak ditemukan di: {$path}");
            return;
        }

        $rows = json_decode(File::get($path), true);

        if (!is_array($rows)) {
            $this->command->error("Format JSON tidak valid.");
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $nama = trim($row['Nama'] ?? '');
                if ($nama === '') continue;

                $menteri = Menteri::where('nama', $nama)->first();
                if (!$menteri) {
                    $this->command->warn("Skip: Menteri tidak ditemukan untuk nama = {$nama}");
                    continue;
                }

                // ===== parse tanggal lahir (aman untuk banyak format) =====
                $tanggalLahirRaw = trim((string)($row['Tanggal Lahir'] ?? ''));
                $tanggalLahir = null;

                if ($tanggalLahirRaw !== '') {
                    try {
                        // Carbon parse fleksibel (contoh: "7 Oct 1966", "1966-10-07", "07/10/1966")
                        $tanggalLahir = Carbon::parse($tanggalLahirRaw)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $tanggalLahir = null; // kalau gagal parse, biarin null
                    }
                }

                // ===== parse kekayaan jadi angka =====
                $rawKekayaan = (string)($row['Kekayaan (Rp)'] ?? '');
                $kekayaan = $rawKekayaan !== ''
                    ? (int) preg_replace('/\D+/', '', $rawKekayaan)
                    : null;

                // ===== catatan optional =====
                // kalau kamu mau, bisa gabung beberapa field jadi catatan
                $catatan = null;
                if (!empty($row['Catatan'])) {
                    $catatan = trim($row['Catatan']);
                }

                DetailMenteri::updateOrCreate(
                    ['menteri_id' => $menteri->id],
                    [
                        'tempat_lahir'  => $row['Tempat Lahir'] ?? null,
                        'tanggal_lahir' => $tanggalLahir,
                        'umur_tahun'    => isset($row['Umur (Tahun)']) ? (int)$row['Umur (Tahun)'] : null,

                        'almamater_sma' => $row['Almamater SMA'] ?? null,
                        'almamater_s1'  => $row['Almamater S1'] ?? null,
                        'almamater_s2'  => $row['Almamater S2'] ?? null,
                        'almamater_s3'  => $row['Almamater S3'] ?? null,

                        'kekayaan_rp'   => $kekayaan,
                        'status_hukum'  => $row['Status Hukum?'] ?? null,

                        'catatan'       => $catatan,
                    ]
                );
            }

            DB::commit();
            $this->command->info("Seeder detail_menteris sukses. Total rows diproses: ".count($rows));
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error("Seeder gagal: ".$e->getMessage());
            throw $e;
        }
    }
}
