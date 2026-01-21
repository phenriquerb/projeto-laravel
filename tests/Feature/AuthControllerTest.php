<?php

namespace Tests\Feature;

use App\Enums\CargoEnum;
use App\Models\Cargo;
use App\Models\Funcionario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_fazer_login_com_credenciais_validas(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar
        Funcionario::factory()->create([
            'login' => 'test.user',
            'password' => bcrypt('password123'),
            'cargo_id' => CargoEnum::TECNICO->value,
        ]);

        // Agir
        $response = $this->postJson('/api/login', [
            'login' => 'test.user',
            'password' => 'password123',
        ]);

        // Verificar
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'funcionario' => ['id', 'nome', 'email'],
            ]);

        $this->assertNotEmpty($response->json('token'));
    }

    public function test_deve_rejeitar_credenciais_invalidas(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar
        Funcionario::factory()->create([
            'login' => 'test.user',
            'password' => bcrypt('password123'),
            'cargo_id' => CargoEnum::TECNICO->value,
        ]);

        // Agir
        $response = $this->postJson('/api/login', [
            'login' => 'test.user',
            'password' => 'wrong_password',
        ]);

        // Verificar
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Credenciais inválidas.',
            ]);
    }

    public function test_deve_rejeitar_usuario_inexistente(): void
    {
        // Agir
        $response = $this->postJson('/api/login', [
            'login' => 'usuario.inexistente',
            'password' => 'password123',
        ]);

        // Verificar
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Credenciais inválidas.',
            ]);
    }

    public function test_deve_fazer_logout_com_token_valido(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar
        $funcionario = Funcionario::factory()->create([
            'login' => 'test.user',
            'password' => bcrypt('password123'),
            'cargo_id' => CargoEnum::TECNICO->value,
        ]);

        $token = $funcionario->createToken('test-token')->plainTextToken;

        // Agir
        $response = $this->postJson('/api/logout', [], [
            'Authorization' => "Bearer {$token}",
        ]);

        // Verificar
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout realizado com sucesso.',
            ]);

        // Verificar que o token foi revogado
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $funcionario->id,
            'tokenable_type' => Funcionario::class,
        ]);
    }

    public function test_deve_retornar_dados_do_usuario_autenticado(): void
    {
        // Preparar cargos
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        // Preparar
        $funcionario = Funcionario::factory()->create([
            'login' => 'test.user',
            'password' => bcrypt('password123'),
            'cargo_id' => CargoEnum::TECNICO->value,
        ]);

        $token = $funcionario->createToken('test-token')->plainTextToken;

        // Agir
        $response = $this->getJson('/api/me', [
            'Authorization' => "Bearer {$token}",
        ]);

        // Verificar
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nome',
                    'email',
                    'cargo' => ['id', 'nome'],
                ],
            ])
            ->assertJson([
                'data' => [
                    'id' => $funcionario->id,
                    'email' => $funcionario->email,
                ],
            ]);
    }
}
