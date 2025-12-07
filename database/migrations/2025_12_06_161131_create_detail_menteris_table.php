<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detail_menteris', function (Blueprint $table) {
            $table->id();

            // relasi ke menteris (tanpa FK constraint)
            $table->unsignedBigInteger('menteri_id')->index();

            // human-readable fields (sesuai sheet "Untuk Aplikasi")
            $table->string('tempat_lahir')->nullable();          // kota/kab
            $table->date('tanggal_lahir')->nullable();
            $table->unsignedTinyInteger('umur_tahun')->nullable();

            $table->string('almamater_sma')->nullable();
            $table->string('almamater_s1')->nullable();
            $table->string('almamater_s2')->nullable();
            $table->string('almamater_s3')->nullable();

            $table->unsignedBigInteger('kekayaan_rp')->nullable(); // nominal
            $table->string('status_hukum')->nullable();            // teks bebas (misal: "bersih", "pernah kasus", dll)

            $table->text('catatan')->nullable(); // opsional kalau kamu mau ruang catatan

            $table->timestamps();

            // biar 1 menteri cuma punya 1 detail
            $table->unique('menteri_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_menteris');
    }
};
