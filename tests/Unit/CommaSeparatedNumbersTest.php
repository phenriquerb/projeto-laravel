<?php

namespace Tests\Unit;

use App\Rules\CommaSeparatedNumbers;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CommaSeparatedNumbersTest extends TestCase
{
    /**
     * Testa se aceita valores válidos de números separados por vírgula
     */
    public function test_deve_aceitar_numeros_separados_por_virgula_validos(): void
    {
        $rule = new CommaSeparatedNumbers;
        $validator = Validator::make(
            ['id' => '1,2,3'],
            ['id' => [$rule]]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * Testa se aceita um único número
     */
    public function test_deve_aceitar_um_unico_numero(): void
    {
        $rule = new CommaSeparatedNumbers;
        $validator = Validator::make(
            ['id' => '123'],
            ['id' => [$rule]]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * Testa se aceita null (deve ser tratado por outras regras como nullable)
     */
    public function test_deve_aceitar_null(): void
    {
        $rule = new CommaSeparatedNumbers;
        $validator = Validator::make(
            ['id' => null],
            ['id' => [$rule]]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * Testa se rejeita strings com caracteres não numéricos
     */
    public function test_deve_rejeitar_strings_com_caracteres_nao_numericos(): void
    {
        $rule = new CommaSeparatedNumbers;
        $validator = Validator::make(
            ['id' => '1,2a,3'],
            ['id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('números inteiros positivos', $validator->errors()->first('id'));
    }

    /**
     * Testa se rejeita strings vazias
     *
     * Nota: Quando a string está vazia, a regra deve falhar.
     * Se o campo for nullable, o null é aceito, mas string vazia não.
     */
    public function test_deve_rejeitar_strings_vazias(): void
    {
        $rule = new CommaSeparatedNumbers;
        // Adiciona 'required' para garantir que o campo seja validado mesmo quando vazio
        $validator = Validator::make(
            ['id' => ''],
            ['id' => ['required', $rule]]
        );

        $this->assertFalse($validator->passes());
    }

    /**
     * Testa se rejeita vírgulas consecutivas
     */
    public function test_deve_rejeitar_virgulas_consecutivas(): void
    {
        $rule = new CommaSeparatedNumbers;
        $validator = Validator::make(
            ['id' => '1,,2'],
            ['id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('vírgulas consecutivas', $validator->errors()->first('id'));
    }

    /**
     * Testa se rejeita valores que não são strings
     */
    public function test_deve_rejeitar_valores_que_nao_sao_strings(): void
    {
        $rule = new CommaSeparatedNumbers;
        $validator = Validator::make(
            ['id' => 123],
            ['id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('deve ser uma string', $validator->errors()->first('id'));
    }

    /**
     * Testa se aceita números com espaços (que serão removidos)
     */
    public function test_deve_aceitar_numeros_com_espacos(): void
    {
        $rule = new CommaSeparatedNumbers;
        $validator = Validator::make(
            ['id' => '1, 2, 3'],
            ['id' => [$rule]]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * Testa se rejeita números negativos
     */
    public function test_deve_rejeitar_numeros_negativos(): void
    {
        $rule = new CommaSeparatedNumbers;
        $validator = Validator::make(
            ['id' => '1,-2,3'],
            ['id' => [$rule]]
        );

        $this->assertFalse($validator->passes());
    }
}
