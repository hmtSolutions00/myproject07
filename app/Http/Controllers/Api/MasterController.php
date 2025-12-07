<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

class MasterController extends Controller
{
    public function index()
    {
        return response()->json([
            'kementerian' => MasterKementerian::orderBy('nama_kementerian')->get(),
            'jenis_kelamin' => MasterJenisKelamin::orderBy('kode_umap')->get(),
            'provinsi' => MasterProvinsi::orderBy('kode_umap')->get(),
            'umur_kategori' => MasterUmurKategori::orderBy('kode_umap')->get(),
            'partai' => MasterPartai::orderBy('kode_umap')->get(),
            'jabatan_rangkap' => MasterJabatanRangkap::orderBy('kode_umap')->get(),
            'dpr_mpr' => MasterDprMpr::orderBy('kode_umap')->get(),
            'militer_polisi' => MasterMiliterPolisi::orderBy('kode_umap')->get(),
            'pendidikan_umum' => MasterPendidikan::where('jenjang_default','umum')->orderBy('kode_umap')->get(),
            'pendidikan_s2s3' => MasterPendidikan::where('jenjang_default','s2s3')->orderBy('kode_umap')->get(),
            'korupsi_level' => MasterKorupsiLevel::orderBy('kode_umap')->get(),
            'harta_level' => MasterHartaLevel::orderBy('kode_umap')->get(),
        ]);
    }
}
