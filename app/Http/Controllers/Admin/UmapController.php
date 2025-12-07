<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menteri;

class UmapController extends Controller
{
    public function index()
    {
        $menteris = Menteri::with([
            'jenisKelamin','provinsiLahir','umurKategori','partai',
            'jabatanRangkap','dprMpr','militerPolisi',
            'lokasiSma','lokasiS1','lokasiS2','lokasiS3',
            'pendidikanS1','pendidikanS2S3','korupsiLevel','hartaLevel',
        ])
        ->orderBy('nama')
        ->get();

        return view('admin.umap.index', compact('menteris'));
    }
}
