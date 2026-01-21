<?php

namespace Tests\Feature;

use App\Enums\CargoEnum;
use App\Models\Cargo;
use App\Models\Funcionario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FuncionarioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_retornar_lista_de_funcionarios_com_estrutura_correta(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Autenticar usuário
        $usuario = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        Sanctum::actingAs($usuario, ['*']);

        // Preparar: Criar 3 funcionários usando a factory
        Funcionario::factory()->count(3)->create(['cargo_id' => CargoEnum::TECNICO->value]);

        // Agir: Chamar a rota da API
        $response = $this->getJson('/api/funcionarios');

        // Verificar: Status 200 e se o Resource formatou o JSON corretamente
        $response->assertStatus(200)
            ->assertJsonCount(4, 'data') // 3 + 1 (usuario autenticado)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nome',
                        'email',
                        'cargo' => [
                            'id',
                            'nome',
                        ],
                    ],
                ],
            ]);
    }

    public function test_deve_retornar_lista_vazia_quando_nao_houver_funcionarios(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);

        // Autenticar usuário
        $usuario = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        Sanctum::actingAs($usuario, ['*']);

        // Deletar o usuário para retornar lista vazia
        Funcionario::where('id', '!=', $usuario->id)->delete();
        $usuario->delete();

        // Agir: Chamar a rota da API sem criar funcionários
        $response = $this->getJson('/api/funcionarios');

        // Verificar: Status 200 e array vazio dentro de "data"
        $response->assertStatus(200)
            ->assertJsonCount(0, 'data')
            ->assertJson(['data' => []]);
    }

    public function test_deve_filtrar_funcionarios_por_id(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Autenticar usuário
        $usuario = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        Sanctum::actingAs($usuario, ['*']);

        // Preparar: Criar 5 funcionários
        $funcionario1 = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);
        $funcionario2 = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);
        Funcionario::factory()->count(3)->create(['cargo_id' => CargoEnum::TECNICO->value]);

        // Agir: Chamar a rota com filtro de ID
        $response = $this->getJson("/api/funcionarios?id={$funcionario1->id},{$funcionario2->id}");

        // Verificar: Deve retornar apenas os 2 funcionários filtrados
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $funcionario1->id])
            ->assertJsonFragment(['id' => $funcionario2->id]);
    }

    public function test_deve_filtrar_funcionarios_por_nome(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Autenticar usuário
        $usuario = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        Sanctum::actingAs($usuario, ['*']);

        // Preparar: Criar funcionários com nomes específicos
        Funcionario::factory()->create([
            'nome' => 'João Silva',
            'email' => 'joao@example.com',
            'cargo_id' => CargoEnum::TECNICO->value,
        ]);

        Funcionario::factory()->create([
            'nome' => 'Maria Santos',
            'email' => 'maria@example.com',
            'cargo_id' => CargoEnum::TECNICO->value,
        ]);

        Funcionario::factory()->create([
            'nome' => 'Pedro Oliveira',
            'email' => 'pedro@example.com',
            'cargo_id' => CargoEnum::TECNICO->value,
        ]);

        // Agir: Filtrar por nome parcial "Silva"
        $response = $this->getJson('/api/funcionarios?nome=Silva');

        // Verificar: Deve retornar apenas "João Silva"
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['nome' => 'João Silva']);
    }
}
