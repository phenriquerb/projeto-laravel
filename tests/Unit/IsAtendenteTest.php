<?php

namespace Tests\Unit;

use App\Models\Cargo;
use App\Models\Funcionario;
use App\Rules\IsAtendente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IsAtendenteTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_aceitar_funcionario_atendente_ativo(): void
    {
        // Criar cargo Atendente
        $cargo = Cargo::create(['nome' => 'Atendente']);

        // Criar funcionário atendente
        $funcionario = Funcionario::create([
            'nome' => 'João Atendente',
            'email' => 'joao@example.com',
            'login' => 'joao.atendente',
            'password' => bcrypt('password'),
            'cargo_id' => $cargo->id,
        ]);

        $rule = new IsAtendente();
        $validator = Validator::make(
            ['atendente_id' => $funcionario->id],
            ['atendente_id' => [$rule]]
        );

        $this->assertTrue($validator->passes());
    }


    public function test_deve_rejeitar_funcionario_com_cargo_diferente(): void
    {
        // Criar cargo Atendente primeiro (ID 1) e depois Técnico (ID 2)
        Cargo::create(['nome' => 'Atendente']);
        $cargoTecnico = Cargo::create(['nome' => 'Tecnico']);

        // Criar funcionário técnico
        $funcionario = Funcionario::create([
            'nome' => 'Pedro Técnico',
            'email' => 'pedro@example.com',
            'login' => 'pedro.tecnico',
            'password' => bcrypt('password'),
            'cargo_id' => $cargoTecnico->id,
        ]);

        $rule = new IsAtendente();
        $validator = Validator::make(
            ['atendente_id' => $funcionario->id],
            ['atendente_id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('não possui o cargo de Atendente', $validator->errors()->first('atendente_id'));
    }

    public function test_deve_rejeitar_funcionario_inexistente(): void
    {
        $rule = new IsAtendente();
        $validator = Validator::make(
            ['atendente_id' => 999],
            ['atendente_id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('não existe', $validator->errors()->first('atendente_id'));
    }

    public function test_deve_rejeitar_valor_nao_numerico(): void
    {
        $rule = new IsAtendente();
        $validator = Validator::make(
            ['atendente_id' => 'abc'],
            ['atendente_id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('deve ser um número', $validator->errors()->first('atendente_id'));
    }
}
