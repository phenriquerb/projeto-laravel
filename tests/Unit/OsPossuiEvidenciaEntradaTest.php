<?php

namespace Tests\Unit;

use App\Enums\CargoEnum;
use App\Models\Cargo;
use App\Models\Cliente;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\OrdemServico;
use App\Models\OsEvidencia;
use App\Rules\OsPossuiEvidenciaEntrada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class OsPossuiEvidenciaEntradaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cargo::create(['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente']);
    }

    public function test_deve_aceitar_os_com_evidencia_de_entrada(): void
    {
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        OsEvidencia::create([
            'ordem_servico_id' => $os->id,
            'path' => 'evidencias/2026/01/teste.jpg',
            'momento' => 'entrada',
        ]);

        $rule = new OsPossuiEvidenciaEntrada($os->id);
        $validator = Validator::make(
            ['status' => 'em_analise'],
            ['status' => [$rule]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_deve_rejeitar_os_sem_evidencia_de_entrada(): void
    {
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        $rule = new OsPossuiEvidenciaEntrada($os->id);
        $validator = Validator::make(
            ['status' => 'em_analise'],
            ['status' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('pelo menos uma evidência de entrada', $validator->errors()->first('status'));
    }

    public function test_deve_rejeitar_os_com_evidencia_de_saida_apenas(): void
    {
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);
        $atendente = Funcionario::factory()->create(['cargo_id' => CargoEnum::ATENDENTE->value]);

        $os = OrdemServico::factory()->create([
            'cliente_id' => $cliente->id,
            'equipamento_id' => $equipamento->id,
            'atendente_id' => $atendente->id,
        ]);

        // Criar evidência de saída (não deve ser aceita)
        OsEvidencia::create([
            'ordem_servico_id' => $os->id,
            'path' => 'evidencias/2026/01/teste.jpg',
            'momento' => 'saida',
        ]);

        $rule = new OsPossuiEvidenciaEntrada($os->id);
        $validator = Validator::make(
            ['status' => 'em_analise'],
            ['status' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('pelo menos uma evidência de entrada', $validator->errors()->first('status'));
    }
}
