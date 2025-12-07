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
        Schema::create('master_militer_polisis', function (Blueprint $table) {
          $table->id();
          $table->string('label')->unique(); // Tidak ada/Militer/Polisi
          $table->unsignedTinyInteger('kode_umap')->unique(); // 0-2
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
        Schema::dropIfExists('master_militer_polisis');
    }
};
