<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenteriController as ApiMenteriController;
use App\Http\Controllers\Api\MasterController as ApiMasterController;

Route::get('/menteri', [ApiMenteriController::class,'index']);

// opsional (kalau dropdown di form mau fetch via API)
Route::get('/masters', [ApiMasterController::class,'index']);
