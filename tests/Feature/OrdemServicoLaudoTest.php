<?php

namespace Tests\Feature;

use App\Enums\CargoEnum;
use App\Models\Cargo;
use App\Models\Cliente;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\OrdemServico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrdemServicoLaudoTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_atualizar_laudo_por_tecnico_responsavel(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        $tecnico = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
            'status' => 'em_analise',
        ]);

        $os->responsaveis()->attach($tecnico->id);

        // Autenticar como técnico responsável
        Sanctum::actingAs($tecnico, ['*']);

        // Agir
        $response = $this->putJson("/api/os/{$os->id}/laudo", [
            'diagnostico_tecnico' => 'Placa-mãe queimada, necessário substituição completa.',
            'valor_total' => 350.00,
        ]);

        // Verificar
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Laudo técnico atualizado com sucesso.',
            ]);

        $this->assertDatabaseHas('ordens_servico', [
            'id' => $os->id,
            'diagnostico_tecnico' => 'Placa-mãe queimada, necessário substituição completa.',
            'valor_total' => 350.00,
            'status' => 'execucao',
        ]);
    }

    public function test_nao_deve_atualizar_laudo_por_tecnico_nao_responsavel(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        $tecnicoResponsavel = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);
        $tecnicoNaoResponsavel = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
            'status' => 'em_analise',
        ]);

        $os->responsaveis()->attach($tecnicoResponsavel->id);

        // Autenticar como técnico NÃO responsável
        Sanctum::actingAs($tecnicoNaoResponsavel, ['*']);

        // Agir
        $response = $this->putJson("/api/os/{$os->id}/laudo", [
            'diagnostico_tecnico' => 'Teste de laudo não autorizado.',
            'valor_total' => 100.00,
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['diagnostico_tecnico']);
    }

    public function test_nao_deve_atualizar_laudo_sem_autenticacao(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        // Agir (sem autenticação)
        $response = $this->putJson("/api/os/{$os->id}/laudo", [
            'diagnostico_tecnico' => 'Teste sem autenticação.',
            'valor_total' => 100.00,
        ]);

        // Verificar
        $response->assertStatus(401);
    }

    public function test_deve_validar_valor_total_obrigatorio(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        $tecnico = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        $os->responsaveis()->attach($tecnico->id);

        Sanctum::actingAs($tecnico, ['*']);

        // Agir - sem valor_total
        $response = $this->putJson("/api/os/{$os->id}/laudo", [
            'diagnostico_tecnico' => 'Teste sem valor total.',
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['valor_total']);
    }

    public function test_deve_validar_valor_total_maior_que_zero(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        $tecnico = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        $os->responsaveis()->attach($tecnico->id);

        Sanctum::actingAs($tecnico, ['*']);

        // Agir - valor_total = 0
        $response = $this->putJson("/api/os/{$os->id}/laudo", [
            'diagnostico_tecnico' => 'Teste com valor zero.',
            'valor_total' => 0,
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['valor_total']);
    }

    public function test_deve_validar_diagnostico_tecnico_obrigatorio(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        $tecnico = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        $os->responsaveis()->attach($tecnico->id);

        Sanctum::actingAs($tecnico, ['*']);

        // Agir - sem diagnostico_tecnico
        $response = $this->putJson("/api/os/{$os->id}/laudo", [
            'valor_total' => 100.00,
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['diagnostico_tecnico']);
    }

    public function test_deve_validar_diagnostico_tecnico_minimo_10_caracteres(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar dados
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        $tecnico = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        $os->responsaveis()->attach($tecnico->id);

        Sanctum::actingAs($tecnico, ['*']);

        // Agir - diagnostico_tecnico com menos de 10 caracteres
        $response = $this->putJson("/api/os/{$os->id}/laudo", [
            'diagnostico_tecnico' => 'Teste',
            'valor_total' => 100.00,
        ]);

        // Verificar
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['diagnostico_tecnico']);
    }
}
