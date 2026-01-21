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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nome = $this->faker->name();
        $login = strtolower(str_replace(' ', '.', $nome));

        return [
            'nome' => $nome,
            'email' => $this->faker->unique()->safeEmail(),
            'login' => $login,
            'password' => bcrypt('password'),
            'cargo_id' => function () {
                return Cargo::factory()->create()->id;
            },
        ];
    }
}
