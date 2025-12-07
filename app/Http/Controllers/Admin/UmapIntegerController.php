<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menteri;
use App\Models\UmapEmbedding;
use App\Services\UmapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UmapIntegerController extends Controller
{
    public function index()
    {
        $menteris = Menteri::with([
            'jenisKelamin','provinsiLahir','umurKategori',
            'partai','jabatanRangkap','dprMpr','militerPolisi',
            'lokasiSma','lokasiS1','lokasiS2','lokasiS3',
            'pendidikanS1','pendidikanS2S3','korupsiLevel','hartaLevel',
        ])
        ->orderBy('nama')
        ->get();

        // bentuk rows integer seperti payload UMAP
        $rows = $menteris->map(function ($m) {
            return [
                'id' => $m->id,
                'nama' => $m->nama,
                'jenis_kelamin' => (int) $m->jenisKelamin->kode_umap,
                'provinsi_lahir' => (int) $m->provinsiLahir->kode_umap,
                'umur' => (int) $m->umurKategori->kode_umap,
                'partai' => (int) $m->partai->kode_umap,
                'jabatan_rangkap' => (int) $m->jabatanRangkap->kode_umap,
                'pernah_menteri' => (int) $m->pernah_menteri,
                'dpr_mpr' => (int) $m->dprMpr->kode_umap,
                'militer_polisi' => (int) $m->militerPolisi->kode_umap,
                'lokasi_sma' => (int) $m->lokasiSma->kode_umap,
                'lokasi_s1' => (int) $m->lokasiS1->kode_umap,
                'lokasi_s2' => (int) (optional($m->lokasiS2)->kode_umap ?? 0),
                'lokasi_s3' => (int) (optional($m->lokasiS3)->kode_umap ?? 0),
                'pendidikan_s1' => (int) $m->pendidikanS1->kode_umap,
                'pendidikan_s2s3' => (int) (optional($m->pendidikanS2S3)->kode_umap ?? 0),
                'korupsi' => (int) $m->korupsiLevel->kode_umap,
                'harta' => (int) $m->hartaLevel->kode_umap,
            ];
        })->values()->all();

        return view('admin.umap-integer.index', compact('rows'));
    }

    public function recompute(UmapService $umapService)
    {
        try {
            $umapService->recomputeAll();
            return back()->with('success', 'Recompute UMAP berhasil. Embedding terbaru sudah dibuat.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Recompute gagal: '.$e->getMessage());
        }
    }

    public function clear()
    {
        try {
            DB::transaction(function () {
                // hapus semuanya
                UmapEmbedding::truncate();
            });

            return back()->with('success', 'Semua data UMAP berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Clear UMAP gagal: '.$e->getMessage());
        }
    }
}
