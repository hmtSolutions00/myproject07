<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\MenteriController;

// admin controllers
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AplikasiController;
use App\Http\Controllers\Admin\UmapController;
use App\Http\Controllers\Admin\UmapIntegerController;
use App\Http\Controllers\Admin\DetailMenteriController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/bandingkan', [CompareController::class,'index'])->name('compare');

// kalau masih dipakai (optional, bisa kamu hapus nanti)
Route::get('/tambah-menteri', [MenteriController::class,'create'])->name('menteri.create');
Route::post('/tambah-menteri', [MenteriController::class,'store'])->name('menteri.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // redirect /admin -> /admin/aplikasi
    Route::get('/', fn () => redirect()->route('admin.aplikasi.index'))
        ->name('dashboard');

    /*
    |========================
    | AUTH ADMIN (PUBLIC)
    |========================
    */

    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |========================
    | ADMIN AREA (PROTECTED)
    |========================
    */
    Route::middleware(['auth','admin'])->group(function () {

        // aplikasi (human readable)
        Route::get('/aplikasi', [AplikasiController::class, 'index'])
            ->name('aplikasi.index');

        Route::get('/aplikasi/{menteri}/edit', [AplikasiController::class, 'edit'])
            ->name('aplikasi.edit');

        Route::put('/aplikasi/{menteri}', [AplikasiController::class, 'update'])
            ->name('aplikasi.update');

        // umap view
        Route::get('/umap', [UmapController::class, 'index'])
            ->name('umap.index');

        // umap integer + actions
        Route::get('/umap-integer', [UmapIntegerController::class, 'index'])
            ->name('umap_integer.index');

        Route::post('/umap-integer/recompute', [UmapIntegerController::class, 'recompute'])
            ->name('umap.integer.recompute');

        Route::post('/umap-integer/clear', [UmapIntegerController::class, 'clear'])
            ->name('umap.integer.clear');
                // ✅ halaman baru Detail Menteri
        Route::get('/detail-menteri', [DetailMenteriController::class, 'index'])
            ->name('detail_menteri.index');
    });
});
