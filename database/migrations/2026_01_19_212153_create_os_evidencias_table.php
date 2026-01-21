<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('os_evidencias', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ordem_servico_id');
            $table->foreign('ordem_servico_id')->references('id')->on('ordens_servico')->onDelete('cascade');
            $table->string('path'); // Caminho no Storage (DigitalOcean Spaces / S3)
            $table->string('legenda')->nullable();
            $table->enum('momento', ['entrada', 'saida']); // Foto de quando chegou ou quando saiu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('os_evidencias');
    }
};
