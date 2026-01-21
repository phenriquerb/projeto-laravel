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
        Schema::create('os_responsaveis', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ordem_servico_id');
            $table->foreign('ordem_servico_id')->references('id')->on('ordens_servico')->onDelete('cascade');
            $table->unsignedInteger('funcionario_id');
            $table->foreign('funcionario_id')->references('id')->on('funcionarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('os_responsaveis');
    }
};
