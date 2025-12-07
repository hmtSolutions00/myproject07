<?php

namespace App\Services;

use App\Models\Menteri;
use App\Models\UmapEmbedding;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UmapService
{
    /**
     * Palette warna aman (tanpa hitam).
     * Silakan tambah / ganti kapanpun, tapi jangan ubah urutan
     * kalau mau warna lama tetap konsisten.
     */
    protected array $palette = [
        "#FF6384","#36A2EB","#FFCE56","#4BC0C0","#9966FF",
        "#FF9F40","#00C49A","#F15BB5","#00BBF9","#9BDEAC",
        "#9AD0F5","#FFB1C1","#A3E635","#FBBF24","#38BDF8",
        "#C084FC","#FB7185","#34D399","#F472B6","#60A5FA",
    ];

    /**
     * Tentukan warna stabil PER ORANG (unik per menteri).
     * Basis: hash(id) -> index palette.
     */
    protected function pickColorForMenteri(Menteri $m): string
    {
        // hash stabil dari id (lebih nyebar daripada modulo biasa)
        $hash = abs(crc32((string) $m->id));
        $idx  = $hash % count($this->palette);

        $color = $this->palette[$idx];

        // safeguard kalau palette suatu saat mengandung hitam
        if (in_array(strtolower($color), ['#000', '#000000', 'black'], true)) {
            $color = "#36A2EB";
        }

        return $color;
    }

    /**
     * Generate ulang embedding untuk semua menteri approved.
     */
    public function recomputeAll(string $batchTag = null): void
    {
        $batchTag = $batchTag ?? ('v' . now()->format('Ymd_His'));

        // 1) Ambil semua menteri approved + eager load relasi yg dipakai fitur
        $menteris = Menteri::with([
            'jenisKelamin','provinsiLahir','umurKategori',
            'partai','jabatanRangkap','dprMpr','militerPolisi',
            'lokasiSma','lokasiS1','lokasiS2','lokasiS3',
            'pendidikanS1','pendidikanS2S3','korupsiLevel','hartaLevel',
        ])->where('status', 'approved')->get();

        // kalau kosong, stop aman
        if ($menteris->isEmpty()) {
            return;
        }

        // 2) Bentuk fitur numerik sesuai encoding excel kamu
        $payload = $menteris->map(function ($m) {
            return [
                'id' => $m->id,
                'features' => [
                    (int) $m->jenisKelamin->kode_umap,
                    (int) $m->provinsiLahir->kode_umap,
                    (int) $m->umurKategori->kode_umap,
                    (int) $m->partai->kode_umap,
                    (int) $m->jabatanRangkap->kode_umap,
                    (int) $m->pernah_menteri,
                    (int) $m->dprMpr->kode_umap,
                    (int) $m->militerPolisi->kode_umap,
                    (int) $m->lokasiSma->kode_umap,
                    (int) $m->lokasiS1->kode_umap,
                    (int) optional($m->lokasiS2)->kode_umap ?? 0,
                    (int) optional($m->lokasiS3)->kode_umap ?? 0,
                    (int) $m->pendidikanS1->kode_umap,
                    (int) optional($m->pendidikanS2S3)->kode_umap ?? 0,
                    (int) $m->korupsiLevel->kode_umap,
                    (int) $m->hartaLevel->kode_umap,
                ],
            ];
        })->values()->all();

        // simpan payload json sementara
        $inputFile  = "umap/input_{$batchTag}.json";
        $outputFile = "umap/output_{$batchTag}.json";

        Storage::disk('local')->put($inputFile, json_encode($payload));

        $inputPath  = storage_path("app/{$inputFile}");
        $outputPath = storage_path("app/{$outputFile}");

        // 3) Panggil Node UMAP
        $nodeBin    = 'node';
        $scriptPath = base_path('storage/umap/run-umap.js');

        $cmd = escapeshellcmd($nodeBin)
            . ' ' . escapeshellarg($scriptPath)
            . ' ' . escapeshellarg($inputPath)
            . ' ' . escapeshellarg($outputPath);

        exec($cmd, $out, $code);

        if ($code !== 0) {
            throw new \Exception("UMAP Node process gagal:\n" . implode("\n", $out));
        }

        if (!file_exists($outputPath)) {
            throw new \Exception("UMAP output file tidak ditemukan: {$outputPath}\nOutput Node:\n" . implode("\n", $out));
        }

        $json   = file_get_contents($outputPath);
        $result = json_decode($json, true);

        if (!is_array($result) || count($result) === 0) {
            throw new \Exception("UMAP output kosong / tidak valid.\nIsi file:\n{$json}");
        }

        // lookup menteri by id
        $menteriById = $menteris->keyBy('id');

        // 4) Matikan embedding lama
        UmapEmbedding::where('is_active', true)->update(['is_active' => false]);

        // 5) Insert embedding baru + color_code per-orang
        DB::transaction(function () use ($result, $batchTag, $menteriById) {
            foreach ($result as $r) {
                $m = $menteriById->get($r['id']);

                $color = $m ? $this->pickColorForMenteri($m) : "#36A2EB";

                UmapEmbedding::create([
                    'menteri_id' => $r['id'],
                    'umap_x'     => $r['umap_x'],
                    'umap_y'     => $r['umap_y'],
                    'color_code' => $color,
                    'batch_tag'  => $batchTag,
                    'is_active'  => true,
                ]);
            }
        });
    }
}
