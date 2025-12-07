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
        Schema::create('master_korupsi_levels', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique(); // 1-5 label kualitatif
            $table->unsignedTinyInteger('kode_umap')->unique(); // 1-5
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
        Schema::dropIfExists('master_korupsi_levels');
    }
};
