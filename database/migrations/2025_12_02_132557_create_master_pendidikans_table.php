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
        Schema::create('master_pendidikans', function (Blueprint $table) {
            $table->id();
            $table->string('label_jurusan')->unique();
            $table->unsignedTinyInteger('kode_umap'); // 0-32
            $table->enum('jenjang_default', ['s1', 's2s3', 'umum'])->default('umum');
            $table->timestamps();

            $table->unique(['kode_umap', 'jenjang_default']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_pendidikans');
    }
};
