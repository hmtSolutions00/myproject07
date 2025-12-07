<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menteris', function (Blueprint $table) {
            $table->id();

            // Free input
            $table->string('nama');
            $table->text('foto_path')->nullable();

            // Master-based (no FK constraints here)
            $table->unsignedBigInteger('kementerian_id');
            $table->unsignedBigInteger('jenis_kelamin_id');
            $table->unsignedBigInteger('provinsi_lahir_id');
            $table->unsignedBigInteger('umur_kategori_id');
            $table->unsignedBigInteger('partai_id');
            $table->unsignedBigInteger('jabatan_rangkap_id');

            // Encoded booleans / small enums
            $table->unsignedTinyInteger('pernah_menteri')->default(0); // 0/1

            $table->unsignedBigInteger('dpr_mpr_id');
            $table->unsignedBigInteger('militer_polisi_id');

            // Lokasi pendidikan (pakai master_provinsis)
            $table->unsignedBigInteger('lokasi_sma_id');
            $table->unsignedBigInteger('lokasi_s1_id');
            $table->unsignedBigInteger('lokasi_s2_id')->nullable();
            $table->unsignedBigInteger('lokasi_s3_id')->nullable();

            // Pendidikan jurusan
            $table->unsignedBigInteger('pendidikan_s1_id');
            $table->unsignedBigInteger('pendidikan_s2s3_id')->nullable(); 
            // nilai 0 "tidak ada" ditangani via master row, jadi nullable juga aman

            // Level-level skala
            $table->unsignedBigInteger('korupsi_level_id');
            $table->unsignedBigInteger('harta_level_id');

            // Public submission management
            $table->enum('status', ['approved', 'pending', 'rejected'])
                  ->default('approved');
            $table->string('submitted_by_ip')->nullable();

            $table->timestamps();

            // Index untuk performa API/UMAP
            $table->index('status');
            $table->index('kementerian_id');
            $table->index('provinsi_lahir_id');
            $table->index('partai_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menteris');
    }
};
