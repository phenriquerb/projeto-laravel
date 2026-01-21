<?php

namespace Tests\Unit;

use App\Enums\CargoEnum;
use App\Models\Cargo;
use App\Models\Funcionario;
use App\Rules\IsTecnico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IsTecnicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_aceitar_funcionario_tecnico(): void
    {
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        $cargo = Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        $funcionario = Funcionario::create([
            'nome' => 'Maria Técnica',
            'email' => 'maria@example.com',
            'login' => 'maria.tecnica',
            'password' => bcrypt('password'),
            'cargo_id' => $cargo->id,
        ]);

        $rule = new IsTecnico();
        $validator = Validator::make(
            ['tecnico_id' => $funcionario->id],
            ['tecnico_id' => [$rule]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_deve_rejeitar_funcionario_com_cargo_diferente(): void
    {
        $cargoAtendente = Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        $funcionario = Funcionario::create([
            'nome' => 'João Atendente',
            'email' => 'joao@example.com',
            'login' => 'joao.atendente',
            'password' => bcrypt('password'),
            'cargo_id' => $cargoAtendente->id,
        ]);

        $rule = new IsTecnico();
        $validator = Validator::make(
            ['tecnico_id' => $funcionario->id],
            ['tecnico_id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('não possui o cargo de Técnico', $validator->errors()->first('tecnico_id'));
    }

    public function test_deve_rejeitar_funcionario_inexistente(): void
    {
        $rule = new IsTecnico();
        $validator = Validator::make(
            ['tecnico_id' => 999],
            ['tecnico_id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('não existe', $validator->errors()->first('tecnico_id'));
    }

    public function test_deve_rejeitar_valor_nao_numerico(): void
    {
        $rule = new IsTecnico();
        $validator = Validator::make(
            ['tecnico_id' => 'abc'],
            ['tecnico_id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('deve ser um número', $validator->errors()->first('tecnico_id'));
    }
}
