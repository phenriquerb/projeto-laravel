<?php

namespace Tests\Unit;

use App\Rules\MaximoTecnicosPorOS;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class MaximoTecnicosPorOSTest extends TestCase
{
    public function test_deve_aceitar_array_com_1_tecnico(): void
    {
        $rule = new MaximoTecnicosPorOS();
        $validator = Validator::make(
            ['tecnicos' => [1]],
            ['tecnicos' => [$rule]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_deve_aceitar_array_com_3_tecnicos(): void
    {
        $rule = new MaximoTecnicosPorOS();
        $validator = Validator::make(
            ['tecnicos' => [1, 2, 3]],
            ['tecnicos' => [$rule]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_deve_rejeitar_array_vazio(): void
    {
        $rule = new MaximoTecnicosPorOS();
        $validator = Validator::make(
            ['tecnicos' => []],
            ['tecnicos' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('pelo menos um técnico', $validator->errors()->first('tecnicos'));
    }

    public function test_deve_rejeitar_array_com_mais_de_3_tecnicos(): void
    {
        $rule = new MaximoTecnicosPorOS();
        $validator = Validator::make(
            ['tecnicos' => [1, 2, 3, 4]],
            ['tecnicos' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('Máximo de 3 técnicos', $validator->errors()->first('tecnicos'));
    }

    public function test_deve_rejeitar_tecnicos_duplicados(): void
    {
        $rule = new MaximoTecnicosPorOS();
        $validator = Validator::make(
            ['tecnicos' => [1, 2, 1]],
            ['tecnicos' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('duplicados', $validator->errors()->first('tecnicos'));
    }

    public function test_deve_rejeitar_valor_nao_array(): void
    {
        $rule = new MaximoTecnicosPorOS();
        $validator = Validator::make(
            ['tecnicos' => 'nao-array'],
            ['tecnicos' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('deve ser um array', $validator->errors()->first('tecnicos'));
    }
}
