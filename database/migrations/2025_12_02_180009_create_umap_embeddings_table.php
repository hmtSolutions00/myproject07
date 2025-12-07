<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('umap_embeddings', function (Blueprint $table) {
            $table->id();

            // relasi ke menteris (tanpa FK constraint)
            $table->unsignedBigInteger('menteri_id');

            // koordinat hasil UMAP
            $table->double('umap_x');
            $table->double('umap_y');

            // warna titik (HEX). jangan pernah simpan #000000
            $table->string('color_code', 7)->nullable(); // contoh: "#36A2EB"

            // versi/batch agar bisa regenerate berkali-kali
            $table->string('batch_tag')->default('v1'); 
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('menteri_id');
            $table->index(['batch_tag','is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('umap_embeddings');
    }
};
