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
        Schema::create('master_jabatan_rangkaps', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique(); // Independen/Partai/Pemerintah/BUMN
            $table->unsignedTinyInteger('kode_umap')->unique(); // 1-4
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
        Schema::dropIfExists('master_jabatan_rangkaps');
    }
};
