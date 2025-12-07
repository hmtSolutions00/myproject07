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
        Schema::create('master_umur_kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique(); // 10-an, 20-an, dst
            $table->unsignedTinyInteger('kode_umap')->unique();
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
        Schema::dropIfExists('master_umur_kategoris');
    }
};
