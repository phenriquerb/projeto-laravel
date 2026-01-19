<?php

namespace Database\Factories;

use App\Models\Cargo;
use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Funcionario>
 */
class FuncionarioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Funcionario::class;

    /**
     * Sequenciador estático para IDs
     */
    private static int $idCounter = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => self::$idCounter++,
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'ativo' => true,
            'cargo_id' => function () {
                return Cargo::factory()->create()->id;
            },
        ];
    }
}
