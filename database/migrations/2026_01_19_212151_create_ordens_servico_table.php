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
        Schema::create('ordens_servico', function (Blueprint $table) {
            $table->increments('id');
            $table->string('protocolo')->unique(); // Ex: 202601-001 (unique já cria índice)
            $table->unsignedInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->unsignedInteger('equipamento_id');
            $table->foreign('equipamento_id')->references('id')->on('equipamentos');
            $table->unsignedInteger('atendente_id');
            $table->foreign('atendente_id')->references('id')->on('funcionarios');

            $table->text('relato_cliente'); // O que o cliente disse
            $table->text('diagnostico_tecnico')->nullable(); // Preenchido depois

            $table->enum('status', ['aberta', 'em_analise', 'aguardando_pecas', 'execucao', 'concluida', 'cancelada'])
                ->default('aberta');

            $table->enum('prioridade', ['baixa', 'media', 'alta', 'critica'])->default('media');

            $table->decimal('valor_total', 10, 2)->default(0);
            $table->timestamp('data_conclusao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordens_servico');
    }
};
