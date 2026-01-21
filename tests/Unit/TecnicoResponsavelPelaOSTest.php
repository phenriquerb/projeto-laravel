<?php

namespace Tests\Unit;

use App\Enums\CargoEnum;
use App\Models\Cargo;
use App\Models\Cliente;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\OrdemServico;
use App\Rules\TecnicoResponsavelPelaOS;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TecnicoResponsavelPelaOSTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_aceitar_tecnico_responsavel_pela_os(): void
    {
        // Preparar
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

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

        $rule = new TecnicoResponsavelPelaOS($tecnico->id, $os->id);

        // Agir
        $validator = Validator::make(
            ['diagnostico' => 'Teste'],
            ['diagnostico' => ['required', $rule]]
        );

        // Verificar
        $this->assertTrue($validator->passes());
    }

    public function test_deve_rejeitar_tecnico_nao_responsavel(): void
    {
        // Preparar
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);
        $tecnicoResponsavel = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);
        $tecnicoNaoResponsavel = Funcionario::factory()->create(['cargo_id' => CargoEnum::TECNICO->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        $os->responsaveis()->attach($tecnicoResponsavel->id);

        $rule = new TecnicoResponsavelPelaOS($tecnicoNaoResponsavel->id, $os->id);

        // Agir
        $validator = Validator::make(
            ['diagnostico' => 'Teste'],
            ['diagnostico' => ['required', $rule]]
        );

        // Verificar
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('diagnostico', $validator->errors()->toArray());
    }

    public function test_deve_rejeitar_funcionario_nao_tecnico(): void
    {
        // Preparar
        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
        Cargo::create(['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico']);

        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        $rule = new TecnicoResponsavelPelaOS($atendente->id, $os->id);

        // Agir
        $validator = Validator::make(
            ['diagnostico' => 'Teste'],
            ['diagnostico' => ['required', $rule]]
        );

        // Verificar
        $this->assertFalse($validator->passes());
    }
}
