<?php

namespace Database\Factories;

use App\Models\OrdemServico;
use App\Models\OsEvidencia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OsEvidencia>
 */
class OsEvidenciaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = OsEvidencia::class;

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
            'ordem_servico_id' => OrdemServico::factory(),
            'path' => 'evidencias/'.$this->faker->uuid().'.jpg',
            'legenda' => $this->faker->optional()->sentence(),
            'momento' => $this->faker->randomElement(['entrada', 'saida']),
        ];
    }
}
