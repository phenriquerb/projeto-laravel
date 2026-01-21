<?php

namespace Tests\Unit;

use App\Models\Cliente;
use App\Models\Equipamento;
use App\Rules\EquipamentoPertenceAoCliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class EquipamentoPertenceAoClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_aceitar_equipamento_do_proprio_cliente(): void
    {
        // Criar cliente e equipamento
        $cliente = Cliente::factory()->create();
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente->id]);

        $rule = new EquipamentoPertenceAoCliente($cliente->id);
        $validator = Validator::make(
            ['equipamento_id' => $equipamento->id],
            ['equipamento_id' => [$rule]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_deve_rejeitar_equipamento_de_outro_cliente(): void
    {
        // Criar dois clientes diferentes
        $cliente1 = Cliente::factory()->create();
        $cliente2 = Cliente::factory()->create();

        // Equipamento pertence ao cliente 2
        $equipamento = Equipamento::factory()->create(['cliente_id' => $cliente2->id]);

        // Tentar validar com cliente 1
        $rule = new EquipamentoPertenceAoCliente($cliente1->id);
        $validator = Validator::make(
            ['equipamento_id' => $equipamento->id],
            ['equipamento_id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('não pertence ao cliente', $validator->errors()->first('equipamento_id'));
    }

    public function test_deve_rejeitar_equipamento_inexistente(): void
    {
        $cliente = Cliente::factory()->create();

        $rule = new EquipamentoPertenceAoCliente($cliente->id);
        $validator = Validator::make(
            ['equipamento_id' => 999],
            ['equipamento_id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('não existe', $validator->errors()->first('equipamento_id'));
    }

    public function test_deve_rejeitar_valor_nao_numerico(): void
    {
        $cliente = Cliente::factory()->create();

        $rule = new EquipamentoPertenceAoCliente($cliente->id);
        $validator = Validator::make(
            ['equipamento_id' => 'abc'],
            ['equipamento_id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('deve ser um número', $validator->errors()->first('equipamento_id'));
    }
}
