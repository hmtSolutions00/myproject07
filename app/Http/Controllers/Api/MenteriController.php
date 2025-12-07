<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menteri;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MenteriController extends Controller
{
    public function index()
    {
        $menteris = Menteri::with([
            'kementerian','jenisKelamin','provinsiLahir','umurKategori',
            'partai','jabatanRangkap','dprMpr','militerPolisi',
            'lokasiSma','lokasiS1','lokasiS2','lokasiS3',
            'pendidikanS1','pendidikanS2S3','korupsiLevel','hartaLevel',
            'umapEmbedding',
            'detail', // ✅ relasi detail human-readable kamu
        ])
        ->where('status', 'approved')
        ->get();

        return $menteris->map(function ($m) {
            $emb = $m->umapEmbedding; // embedding aktif, bisa null
            $det = $m->detail;        // detail, bisa null

            // ===== fallback dari tabel master =====
            $fallbackProvLahir = $m->provinsiLahir->nama ?? null;
            $fallbackSma       = $m->lokasiSma->nama ?? null;
            $fallbackS1        = $m->lokasiS1->nama ?? null;
            $fallbackS2        = optional($m->lokasiS2)->nama;
            $fallbackS3        = optional($m->lokasiS3)->nama;

            // ===== format tanggal lahir aman =====
            $tglLahir = null;
            if ($det && !empty($det->tanggal_lahir)) {
                try {
                    $tglLahir = Carbon::parse($det->tanggal_lahir)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $tglLahir = null;
                }
            }
            $pick = function($detailVal, $baseVal, $fallback = null) {
    if (!is_null($detailVal) && $detailVal !== '') return $detailVal;
    if (!is_null($baseVal)   && $baseVal   !== '') return $baseVal;
    return $fallback;
};

            return [
                // =========================
                // BASIC
                // =========================
                'id'          => $m->id,
                'nama'        => $m->nama,
                'foto_url'    => $m->foto_path,
                'kementerian' => $m->kementerian->nama_kementerian ?? null,

                // =========================
                // MASTER HUMAN (UMAP LAMA)
                // =========================
                'jenis_kelamin'   => $m->jenisKelamin->label ?? null,
                'provinsi_lahir'  => $fallbackProvLahir,
                'umur'            => $m->umurKategori->label ?? null,
                'partai'          => $m->partai->nama_partai ?? ($m->partai->label ?? null),
                'jabatan_rangkap' => $m->jabatanRangkap->label ?? null,
                'pernah_menteri'  => (int) $m->pernah_menteri,

                'dpr_mpr'        => $m->dprMpr->label ?? null,
                'militer_polisi' => $m->militerPolisi->label ?? null,

                'lokasi_sma' => $fallbackSma,
                'lokasi_s1'  => $fallbackS1,
                'lokasi_s2'  => $fallbackS2,
                'lokasi_s3'  => $fallbackS3,

                'pendidikan_s1'   => $m->pendidikanS1->label_jurusan ?? ($m->pendidikanS1->label ?? null),
                'pendidikan_s2s3' => optional($m->pendidikanS2S3)->label_jurusan
                                     ?? optional($m->pendidikanS2S3)->label,

                'korupsi_level' => $m->korupsiLevel->label ?? null,
                'harta_level'   => $m->hartaLevel->label ?? null,

                // =========================
                // UMAP ACTIVE
                // =========================
                'umap_x'     => optional($emb)->umap_x,
                'umap_y'     => optional($emb)->umap_y,
                'color_code' => optional($emb)->color_code,

                // =========================
                // DETAIL RAW (BISA NULL)
                // =========================
                'tempat_lahir'  => $det->tempat_lahir ?? null,
                'tanggal_lahir' => $tglLahir,
                'umur_tahun'    => $det->umur_tahun ?? null,

                'almamater_sma' => $det->almamater_sma ?? null,
                'almamater_s1'  => $det->almamater_s1 ?? null,
                'almamater_s2'  => $det->almamater_s2 ?? null,
                'almamater_s3'  => $det->almamater_s3 ?? null,

                'kekayaan_rp'  => $det->kekayaan_rp ?? null,
                'status_hukum' => $det->status_hukum ?? null,
                'catatan'      => $det->catatan ?? null,

                // =========================
                // DISPLAY = DETAIL kalau ada, else fallback master
                // (frontend pakai ini!)
                // =========================
                'display_tempat_lahir'  => $det->tempat_lahir ?? $fallbackProvLahir,
                'display_tanggal_lahir' => $tglLahir,
                'display_umur_tahun'    => $det->umur_tahun ?? null,

                'display_almamater_sma' => $det->almamater_sma ?? $fallbackSma,
                'display_almamater_s1'  => $det->almamater_s1 ?? $fallbackS1,
                'display_almamater_s2'  => $det->almamater_s2 ?? $fallbackS2,
                'display_almamater_s3'  => $det->almamater_s3 ?? $fallbackS3,

                'display_kekayaan_rp'  => $det->kekayaan_rp ?? null,
                'display_status_hukum' => $det->status_hukum ?? null,
            ];
        });
    }
}
