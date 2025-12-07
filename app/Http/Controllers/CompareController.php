<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index()
    {
        // data UMAP diambil via /api/menteri oleh JS
        return view('compare');
    }
}
