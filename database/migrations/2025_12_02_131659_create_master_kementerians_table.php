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
        Schema::create('master_kementerians', function (Blueprint $table) {
             $table->id();
            $table->string('nama_kementerian')->unique();
            $table->unsignedInteger('kode_umap')->unique();
            $table->unsignedInteger('urutan')->nullable(); // buat sorting UI
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_kementerians');
    }
};
