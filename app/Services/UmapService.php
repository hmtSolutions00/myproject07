<?php

namespace App\Services;

use App\Models\Menteri;
use App\Models\UmapEmbedding;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UmapService
{
    protected array $palette = [
        "#FF6384","#36A2EB","#FFCE56","#4BC0C0","#9966FF",
        "#FF9F40","#00C49A","#F15BB5","#00BBF9","#9BDEAC",
        "#9AD0F5","#FFB1C1","#A3E635","#FBBF24","#38BDF8",
        "#C084FC","#FB7185","#34D399","#F472B6","#60A5FA",
    ];

    /**
     * Get Node.js binary path (hardcoded karena shell_exec disabled)
     */
    protected function getNodePath(): string
    {
        // Langsung return path yang sudah pasti
        $nodePath = '/home/u941929935/.nvm/versions/node/v18.20.4/bin/node';
        
        if (file_exists($nodePath) && is_executable($nodePath)) {
            return $nodePath;
        }
        
        // Jika path di atas tidak ada, coba fallback
        $fallbacks = [
            '/usr/bin/node',
            '/usr/local/bin/node',
        ];
        
        foreach ($fallbacks as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }
        
        throw new \Exception("Node.js binary tidak ditemukan. Expected path: {$nodePath}");
    }

    protected function pickColorForMenteri(Menteri $m): string
    {
        $hash = abs(crc32((string) $m->id));
        $idx  = $hash % count($this->palette);
        $color = $this->palette[$idx];

        if (in_array(strtolower($color), ['#000', '#000000', 'black'], true)) {
            $color = "#36A2EB";
        }

        return $color;
    }

    /**
     * Cek apakah menteri pernah sekolah ke luar negeri
     * Asumsi: kode_umap >= 39 adalah luar negeri (sesuaikan dengan data master)
     */
    protected function cekKeLuarNegeri(Menteri $m): int
    {
        $lokasiS1 = optional($m->lokasiS1)->kode_umap ?? 0;
        $lokasiS2 = optional($m->lokasiS2)->kode_umap ?? 0;
        $lokasiS3 = optional($m->lokasiS3)->kode_umap ?? 0;

        if ($lokasiS1 >= 39 || $lokasiS2 >= 39 || $lokasiS3 >= 39) {
            return 1;
        }

        return 0;
    }

    public function recomputeAll(string $batchTag = null): void
    {
        $batchTag = $batchTag ?? ('v' . now()->format('Ymd_His'));

        $menteris = Menteri::with([
            'jenisKelamin','provinsiLahir','umurKategori',
            'partai','jabatanRangkap','dprMpr','militerPolisi',
            'lokasiSma','lokasiS1','lokasiS2','lokasiS3',
            'pendidikanS1','pendidikanS2S3','korupsiLevel','hartaLevel',
        ])->where('status', 'approved')->get();

        if ($menteris->isEmpty()) {
            return;
        }

        // Bentuk fitur sesuai URUTAN cobaUmap.js (TANPA provinsiLahir)
        $payload = $menteris->map(function ($m) {
            return [
                'id' => $m->id,
                'features' => [
                    (int) $m->jenisKelamin->kode_umap,        // 0: JenisKelamin
                    (int) $m->partai->kode_umap,              // 1: Partai
                    (int) $m->jabatanRangkap->kode_umap,      // 2: JabatanRangkap
                    (int) $m->pernah_menteri,                 // 3: Militer (pernah_menteri)
                    (int) $m->dprMpr->kode_umap,              // 4: DPRAtauMPR
                    (int) $m->militerPolisi->kode_umap,       // 5: MiliterAtauPolisi
                    (int) $m->umurKategori->kode_umap,        // 6: Umur
                    (int) $m->lokasiSma->kode_umap,           // 7: SMA
                    (int) $m->lokasiS1->kode_umap,            // 8: LokasiS1
                    (int) optional($m->lokasiS2)->kode_umap ?? 0, // 9: LokasiS2
                    (int) optional($m->lokasiS3)->kode_umap ?? 0, // 10: LokasiS3
                    (int) $this->cekKeLuarNegeri($m),         // 11: KeLuarNegeri
                    (int) $m->pendidikanS1->kode_umap,        // 12: Pendidikan1
                    (int) optional($m->pendidikanS2S3)->kode_umap ?? 0, // 13: Pendidikan2
                    (int) $m->korupsiLevel->kode_umap,        // 14: Korupsi
                    (int) $m->hartaLevel->kode_umap,          // 15: Harta
                ],
            ];
        })->values()->all();

        $inputFile  = "umap/input_{$batchTag}.json";
        $outputFile = "umap/output_{$batchTag}.json";

        Storage::disk('local')->put($inputFile, json_encode($payload));

        $inputPath  = storage_path("app/{$inputFile}");
        $outputPath = storage_path("app/{$outputFile}");

        // Gunakan getNodePath() untuk mendapatkan path Node.js
        $nodeBin = $this->getNodePath();
        $scriptPath = base_path('storage/umap/run-umap.js');

        $cmd = escapeshellarg($nodeBin)
            . ' ' . escapeshellarg($scriptPath)
            . ' ' . escapeshellarg($inputPath)
            . ' ' . escapeshellarg($outputPath);

        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];

        // Set environment variables
        $env = [
            'PATH' => '/usr/local/bin:/usr/bin:/bin:/home/u941929935/.nvm/versions/node/v18.20.4/bin',
            'HOME' => '/home/u941929935',
        ];

        $process = proc_open($cmd, $descriptorspec, $pipes, null, $env);

        if (!is_resource($process)) {
            throw new \Exception("Tidak bisa menjalankan Node.js process.");
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $code = proc_close($process);

        if ($code !== 0) {
            $out = array_filter(explode("\n", trim($stdout . "\n" . $stderr)));
            throw new \Exception("UMAP Node process gagal (exit code: {$code}):\n" . implode("\n", $out));
        }

        if (!file_exists($outputPath)) {
            throw new \Exception("UMAP output file tidak ditemukan: {$outputPath}");
        }

        $json   = file_get_contents($outputPath);
        $result = json_decode($json, true);

        if (!is_array($result) || count($result) === 0) {
            throw new \Exception("UMAP output kosong / tidak valid.");
        }

        $menteriById = $menteris->keyBy('id');

        UmapEmbedding::where('is_active', true)->update(['is_active' => false]);

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