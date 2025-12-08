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
    /**
     * Mapping hari dalam bahasa Indonesia ke bahasa Inggris
     */
    private $hariMap = [
        'Senin'   => 'Monday',
        'Selasa'  => 'Tuesday',
        'Rabu'    => 'Wednesday',
        'Kamis'   => 'Thursday',
        'Jumat'   => 'Friday',
        'Sabtu'   => 'Saturday',
        'Minggu'  => 'Sunday',
    ];

    /**
     * Mapping bulan dalam bahasa Indonesia ke angka
     */
    private $bulanMap = [
        'Januari'   => '01',
        'Februari'  => '02',
        'Maret'     => '03',
        'April'     => '04',
        'Mei'       => '05',
        'Juni'      => '06',
        'Juli'      => '07',
        'Agustus'   => '08',
        'September' => '09',
        'Oktober'   => '10',
        'November'  => '11',
        'Desember'  => '12',
    ];

    /**
     * Konversi tanggal format Indonesia ke Y-m-d
     * Contoh input: "Jumat, 11 Desember 1959"
     * Output: "1959-12-11"
     */
    private function convertTanggalIndonesia(?string $tanggalStr): ?string
    {
        if (empty($tanggalStr)) {
            return null;
        }

        $tanggalStr = trim($tanggalStr);

        // Hapus nama hari jika ada (misal: "Jumat, ")
        $tanggalStr = preg_replace('/^(Senin|Selasa|Rabu|Kamis|Jumat|Sabtu|Minggu),?\s*/i', '', $tanggalStr);

        // Pattern: "11 Desember 1959" atau "11 Des 1959"
        if (preg_match('/^(\d{1,2})\s+(\w+)\s+(\d{4})$/i', $tanggalStr, $matches)) {
            $tanggal = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $bulanStr = ucfirst(strtolower($matches[2]));
            $tahun = $matches[3];

            // Cek apakah bulan ada di mapping
            if (isset($this->bulanMap[$bulanStr])) {
                $bulan = $this->bulanMap[$bulanStr];
                return "{$tahun}-{$bulan}-{$tanggal}";
            }

            // Jika tidak ketemu, coba parse dengan Carbon (untuk bulan dalam bahasa Inggris)
            try {
                return Carbon::parse($tanggalStr)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        // Fallback: coba parse langsung dengan Carbon
        try {
            return Carbon::parse($tanggalStr)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

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

                // ===== Parse tanggal lahir dengan fungsi custom =====
                $tanggalLahirRaw = trim((string)($row['Tanggal Lahir'] ?? ''));
                $tanggalLahir = $this->convertTanggalIndonesia($tanggalLahirRaw);

                // Debug: tampilkan hasil konversi
                if ($tanggalLahirRaw && !$tanggalLahir) {
                    $this->command->warn("Gagal parse tanggal untuk {$nama}: {$tanggalLahirRaw}");
                }

                // ===== Parse kekayaan jadi angka =====
                $rawKekayaan = (string)($row['Kekayaan (Rp)'] ?? '');
                $kekayaan = $rawKekayaan !== ''
                    ? (int) preg_replace('/\D+/', '', $rawKekayaan)
                    : null;

                // ===== Catatan optional =====
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