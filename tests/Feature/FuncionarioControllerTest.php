<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Funcionario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FuncionarioControllerTest extends TestCase
{
    use RefreshDatabase; // Roda as migrations no banco SQLite em memória

    public function test_deve_retornar_lista_de_funcionarios_com_estrutura_correta(): void
    {
        // Preparar: Criar cargos e funcionários usando as factories
        $cargo = Cargo::factory()->create();
        Funcionario::factory()->count(3)->create(['cargo_id' => $cargo->id]);

        // Agir: Chamar a rota da API
        $response = $this->getJson('/api/funcionarios');

        // Verificar: Status 200 e se o Resource formatou o JSON corretamente
        // O ResourceCollection do Laravel retorna com wrapper "data"
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
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
        // Agir: Chamar a rota da API sem criar funcionários
        // O RefreshDatabase já garante que o banco está limpo
        $response = $this->getJson('/api/funcionarios');

        // Verificar: Status 200 e array vazio dentro de "data"
        $response->assertStatus(200)
            ->assertJsonCount(0, 'data')
            ->assertJson(['data' => []]);
    }

    public function test_deve_filtrar_funcionarios_por_id(): void
    {
        // Preparar: Criar cargos e funcionários
        $cargo = Cargo::factory()->create();
        $funcionario1 = Funcionario::factory()->create(['cargo_id' => $cargo->id]);
        $funcionario2 = Funcionario::factory()->create(['cargo_id' => $cargo->id]);
        Funcionario::factory()->create(['cargo_id' => $cargo->id]);

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
        // Preparar: Criar cargos e funcionários
        $cargo = Cargo::factory()->create();
        Funcionario::factory()->create([
            'nome' => 'João Silva',
            'cargo_id' => $cargo->id,
        ]);
        Funcionario::factory()->create([
            'nome' => 'Maria Santos',
            'cargo_id' => $cargo->id,
        ]);
        Funcionario::factory()->create([
            'nome' => 'Pedro Oliveira',
            'cargo_id' => $cargo->id,
        ]);

        // Agir: Chamar a rota com filtro de nome
        $response = $this->getJson('/api/funcionarios?nome=João');

        // Verificar: Deve retornar apenas o funcionário com nome "João Silva"
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['nome' => 'João Silva']);
    }
}
