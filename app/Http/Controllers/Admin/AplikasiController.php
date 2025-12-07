<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menteri;

// master models
use App\Models\MasterKementerian;
use App\Models\MasterJenisKelamin;
use App\Models\MasterProvinsi;
use App\Models\MasterUmurKategori;
use App\Models\MasterPartai;
use App\Models\MasterJabatanRangkap;
use App\Models\MasterDprMpr;
use App\Models\MasterMiliterPolisi;
use App\Models\MasterPendidikan;
use App\Models\MasterKorupsiLevel;
use App\Models\MasterHartaLevel;

use Illuminate\Http\Request;

class AplikasiController extends Controller
{
    public function index()
    {
        $menteris = Menteri::with([
            'kementerian','jenisKelamin','provinsiLahir','umurKategori',
            'partai','jabatanRangkap','dprMpr','militerPolisi',
            'lokasiSma','lokasiS1','lokasiS2','lokasiS3',
            'pendidikanS1','pendidikanS2S3','korupsiLevel','hartaLevel',
            'umapEmbedding'
        ])->orderBy('nama')->get();

        return view('admin.aplikasi.index', compact('menteris'));
    }

    public function edit(Menteri $menteri)
    {
        $menteri->load([
            'kementerian','jenisKelamin','provinsiLahir','umurKategori',
            'partai','jabatanRangkap','dprMpr','militerPolisi',
            'lokasiSma','lokasiS1','lokasiS2','lokasiS3',
            'pendidikanS1','pendidikanS2S3','korupsiLevel','hartaLevel'
        ]);

        $kementerians    = MasterKementerian::orderBy('nama_kementerian')->get();
        $jenisKalamins   = MasterJenisKelamin::orderBy('kode_umap')->get();
        $provinsis       = MasterProvinsi::orderBy('kode_umap')->get();
        $umurKategoris   = MasterUmurKategori::orderBy('kode_umap')->get();
        $partais         = MasterPartai::orderBy('kode_umap')->get();
        $jabatanRangkaps = MasterJabatanRangkap::orderBy('kode_umap')->get();
        $dprMprs         = MasterDprMpr::orderBy('kode_umap')->get();
        $militerPolisis  = MasterMiliterPolisi::orderBy('kode_umap')->get();

        // S1 tetap umum
        $pendidikanUmum = MasterPendidikan::where('jenjang_default','umum')
                            ->orderBy('kode_umap')->get();

        /**
         * FIX PENTING:
         * Jurusan S2/S3 -> ambil SEMUA jurusan (karena data master kamu tidak konsisten di jenjang_default)
         * lalu default kode_umap=0 dipakai sebagai "Tidak Ada / Default".
         */
        $pendidikanS2S3 = MasterPendidikan::orderBy('kode_umap')->get();

        $korupsis = MasterKorupsiLevel::orderBy('kode_umap')->get();
        $hartas   = MasterHartaLevel::orderBy('kode_umap')->get();

        $prov0_id = MasterProvinsi::where('kode_umap',0)->value('id');
        $pend0_id = MasterPendidikan::where('kode_umap',0)->value('id');

        return view('admin.aplikasi.edit', compact(
            'menteri',
            'kementerians','jenisKalamins','provinsis','umurKategoris',
            'partais','jabatanRangkaps','dprMprs','militerPolisis',
            'pendidikanUmum','pendidikanS2S3','korupsis','hartas',
            'prov0_id','pend0_id'
        ));
    }

    public function update(Request $request, Menteri $menteri)
    {
        $validated = $request->validate([
            'nama' => ['required','string','max:255'],
            'foto_path' => ['nullable','string'],

            'kementerian_id' => ['required','integer'],
            'jenis_kelamin_id' => ['required','integer'],
            'provinsi_lahir_id' => ['required','integer'],
            'umur_kategori_id' => ['required','integer'],
            'partai_id' => ['required','integer'],
            'jabatan_rangkap_id' => ['required','integer'],
            'pernah_menteri' => ['required','in:0,1'],

            'dpr_mpr_id' => ['required','integer'],
            'militer_polisi_id' => ['required','integer'],

            'lokasi_sma_id' => ['required','integer'],
            'lokasi_s1_id' => ['required','integer'],
            'lokasi_s2_id' => ['nullable','integer'],
            'lokasi_s3_id' => ['nullable','integer'],

            'pendidikan_s1_id' => ['required','integer'],
            'pendidikan_s2s3_id' => ['nullable','integer'],

            'korupsi_level_id' => ['required','integer'],
            'harta_level_id' => ['required','integer'],

            'status' => ['required','in:approved,pending,rejected'],
        ]);

        $prov0_id = MasterProvinsi::where('kode_umap',0)->value('id');
        $pend0_id = MasterPendidikan::where('kode_umap',0)->value('id');

        $validated['lokasi_s2_id']       = $validated['lokasi_s2_id']       ?? $prov0_id;
        $validated['lokasi_s3_id']       = $validated['lokasi_s3_id']       ?? $prov0_id;
        $validated['pendidikan_s2s3_id'] = $validated['pendidikan_s2s3_id'] ?? $pend0_id;

        $menteri->update($validated);

        return redirect()
            ->route('admin.aplikasi.index')
            ->with('success','Data menteri berhasil diupdate.');
    }
}
