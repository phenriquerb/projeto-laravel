<?php

namespace Database\Factories;

use App\Models\Funcionario;
use App\Models\OrdemServico;
use App\Models\OsResponsavel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OsResponsavel>
 */
class OsResponsavelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = OsResponsavel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ordem_servico_id' => OrdemServico::factory(),
            'funcionario_id' => Funcionario::factory(),
        ];
    }
}
