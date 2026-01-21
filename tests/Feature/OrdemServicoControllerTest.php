<?php

namespace Tests\Feature;

use App\Enums\CargoEnum;
use App\Jobs\EnviarEmailOrdemServicoAberta;
use App\Models\Cargo;
use App\Models\Cliente;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\OrdemServico;
use App\Models\OsEvidencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrdemServicoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar cargos padrão
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Autenticar um funcionário padrão
        $funcionarioAuth = Funcionario::factory()->create([
            'cargo_id' => CargoEnum::ATENDENTE->value,
            'login' => 'test.auth',
            'password' => bcrypt('password'),
        ]);
        Sanctum::actingAs($funcionarioAuth, ['*']);
    }

    public function test_deve_criar_os_com_sucesso(): void
    {
        Queue::fake();

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        // Agir
        $response = $this->postJson('/api/os', [
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
            'relato_cliente' => 'Notebook não liga, testando se aceita.',
            'prioridade' => 'media',
        ]);

        // Verificar
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'protocolo',
                    'status',
                    'prioridade',
                    'cliente',
                    'equipamento',
                    'atendente',
                    'relato_cliente',
                    'created_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'status' => 'aberta',
                    'prioridade' => 'media',
                ],
            ]);

        // Verificar se o job foi despachado
        Queue::assertPushed(EnviarEmailOrdemServicoAberta::class);
    }

    public function test_nao_deve_permitir_atendente_com_cargo_incorreto(): void
    {
        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $tecnico = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);

        // Agir
        $response = $this->postJson('/api/os', [
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $tecnico->id,
            'relato_cliente' => 'Notebook não liga, testando se aceita.',
            'prioridade' => 'media',
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['atendente_id']);
    }

    public function test_nao_deve_permitir_cliente_inativo(): void
    {
        // Preparar dados
        $cliente = Cliente::factory()->create();
        $cliente->delete(); // Soft delete

        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        // Agir
        $response = $this->postJson('/api/os', [
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
            'relato_cliente' => 'Notebook não liga, testando se aceita.',
            'prioridade' => 'media',
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cliente_id']);
    }

    public function test_nao_deve_permitir_equipamento_de_outro_cliente(): void
    {
        // Preparar dados
        $cliente1 = Cliente::factory()->create();
        $cliente2 = Cliente::factory()->create();
        $equipamentoDoCliente2 = Equipamento::factory()->create(['cliente_id' => $cliente2->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        // Agir - tentando criar OS do cliente1 com equipamento do cliente2
        $response = $this->postJson('/api/os', [
            'cliente_id' => $cliente1->id,
            'equipamento_id' => $equipamentoDoCliente2->id,
            'atendente_id' => $atendente->id,
            'relato_cliente' => 'Notebook não liga, testando se aceita.',
            'prioridade' => 'media',
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['equipamento_id']);
    }

    public function test_deve_gerar_protocolo_unico(): void
    {
        Queue::fake();

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        // Criar primeira OS
        $response1 = $this->postJson('/api/os', [
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
            'relato_cliente' => 'Problema 1 no equipamento.',
            'prioridade' => 'media',
        ]);

        // Criar segunda OS
        $response2 = $this->postJson('/api/os', [
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
            'relato_cliente' => 'Problema 2 no equipamento.',
            'prioridade' => 'alta',
        ]);

        $response1->assertStatus(201);
        $response2->assertStatus(201);

        $protocolo1 = $response1->json('data.protocolo');
        $protocolo2 = $response2->json('data.protocolo');

        // Verificar que os protocolos são diferentes
        $this->assertNotEquals($protocolo1, $protocolo2);

        // Verificar formato do protocolo (YYYYMM-NNN)
        $this->assertMatchesRegularExpression('/^\d{6}-\d{3}$/', $protocolo1);
        $this->assertMatchesRegularExpression('/^\d{6}-\d{3}$/', $protocolo2);
    }

    public function test_deve_enviar_email_apos_criacao(): void
    {
        Queue::fake();

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        // Agir
        $response = $this->postJson('/api/os', [
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
            'relato_cliente' => 'Notebook não liga, testando se aceita.',
            'prioridade' => 'media',
        ]);

        // Verificar
        $response->assertStatus(201);

        // Verificar que o job foi despachado com o ID correto
        Queue::assertPushed(EnviarEmailOrdemServicoAberta::class, function ($job) use ($response) {
            return $job->ordemServicoId === $response->json('data.id');
        });
    }

    public function test_deve_fazer_upload_de_evidencia(): void
    {
        Storage::fake('public');

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        // Criar imagem fake (sem usar GD - apenas arquivo binário)
        $imagem = UploadedFile::fake()->create('evidencia.jpg', 1024, 'image/jpeg'); // 1MB

        // Agir
        $response = $this->postJson("/api/os/{$os->id}/evidencias", [
            'imagem' => $imagem,
            'legenda' => 'Estado ao chegar no balcão',
        ]);

        // Verificar
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'ordem_servico_id',
                    'path',
                    'url',
                    'legenda',
                    'momento',
                    'created_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'momento' => 'entrada',
                    'legenda' => 'Estado ao chegar no balcão',
                ],
            ]);

        // Verificar se o arquivo foi salvo
        $path = $response->json('data.path');
        Storage::disk('public')->assertExists($path);
    }

    public function test_nao_deve_aceitar_arquivo_maior_que_5mb(): void
    {
        Storage::fake('public');

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        // Criar imagem fake de 6MB (sem usar GD)
        $imagem = UploadedFile::fake()->create('evidencia.jpg', 6144, 'image/jpeg'); // 6MB

        // Agir
        $response = $this->postJson("/api/os/{$os->id}/evidencias", [
            'imagem' => $imagem,
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['imagem']);
    }

    public function test_nao_deve_aceitar_arquivo_nao_imagem(): void
    {
        Storage::fake('public');

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        // Criar arquivo PDF fake
        $arquivo = UploadedFile::fake()->create('documento.pdf', 100);

        // Agir
        $response = $this->postJson("/api/os/{$os->id}/evidencias", [
            'imagem' => $arquivo,
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['imagem']);
    }

    public function test_deve_atualizar_status_para_em_analise_com_evidencia(): void
    {
        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
            'status' => 'aberta',
        ]);

        // Adicionar evidência de entrada
        OsEvidencia::create([
            'ordem_servico_id' => $os->id,
            'path' => 'evidencias/2026/01/teste.jpg',
            'momento' => 'entrada',
        ]);

        // Agir
        $response = $this->patchJson("/api/os/{$os->id}/status", [
            'status' => 'em_analise',
        ]);

        // Verificar
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Status atualizado com sucesso.',
            ]);

        $this->assertDatabaseHas('ordens_servico', [
            'id' => $os->id,
            'status' => 'em_analise',
        ]);
    }

    public function test_nao_deve_atualizar_status_sem_evidencia_entrada(): void
    {
        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
            'status' => 'aberta',
        ]);

        // Não adicionar evidência

        // Agir
        $response = $this->patchJson("/api/os/{$os->id}/status", [
            'status' => 'em_analise',
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_deve_atribuir_tecnicos_validos(): void
    {
        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        $tecnico1 = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);
        $tecnico2 = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        // Agir
        $response = $this->postJson("/api/os/{$os->id}/atribuir", [
            'funcionarios_ids' => [$tecnico1->id, $tecnico2->id],
        ]);

        // Verificar
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Técnicos atribuídos com sucesso.',
            ]);

        $this->assertDatabaseHas('os_responsaveis', [
            'ordem_servico_id' => $os->id,
            'funcionario_id' => $tecnico1->id,
        ]);

        $this->assertDatabaseHas('os_responsaveis', [
            'ordem_servico_id' => $os->id,
            'funcionario_id' => $tecnico2->id,
        ]);
    }

    public function test_nao_deve_atribuir_mais_de_3_tecnicos(): void
    {
        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        $tecnicos = Funcionario::factory()->count(4)->create(['cargo_id' => CargoEnum::TECNICO->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        // Agir
        $response = $this->postJson("/api/os/{$os->id}/atribuir", [
            'funcionarios_ids' => $tecnicos->pluck('id')->toArray(),
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['funcionarios_ids']);
    }

    public function test_nao_deve_atribuir_funcionario_nao_tecnico(): void
    {
        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        // Agir - tentar atribuir atendente como técnico
        $response = $this->postJson("/api/os/{$os->id}/atribuir", [
            'funcionarios_ids' => [$atendente->id],
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['funcionarios_ids.0']);
    }

    public function test_deve_enviar_notificacao_websocket_ao_atribuir(): void
    {
        \Illuminate\Support\Facades\Event::fake([\App\Events\OsTecnicoAtribuido::class]);

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        $tecnico1 = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);
        $tecnico2 = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        // Agir
        $response = $this->postJson("/api/os/{$os->id}/atribuir", [
            'funcionarios_ids' => [$tecnico1->id, $tecnico2->id],
        ]);

        // Verificar
        $response->assertStatus(200);

        // Verificar que o evento foi disparado
        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\OsTecnicoAtribuido::class, function ($event) use ($os, $tecnico1, $tecnico2) {
            return $event->ordemServico->id === $os->id
                && in_array($tecnico1->id, $event->funcionariosIds)
                && in_array($tecnico2->id, $event->funcionariosIds);
        });
    }
}
