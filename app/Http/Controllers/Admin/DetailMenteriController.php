<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menteri;

class DetailMenteriController extends Controller
{
    public function index()
    {
        // ambil semua menteri + relasi master + detail human-readable
        $menteris = Menteri::with([
            'detail',
            'kementerian',
            'jenisKelamin',
            'provinsiLahir',
            'umurKategori',
            'partai',
            'jabatanRangkap',
            'dprMpr',
            'militerPolisi',
            'pendidikanS1',
            'pendidikanS2S3',
        ])->orderBy('nama')->get();

        return view('admin.detail_menteri.index', compact('menteris'));
    }
}
