<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Equipamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipamento>
 */
class EquipamentoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Equipamento::class;

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
        $tipos = ['Notebook', 'Smartphone', 'Servidor', 'Desktop', 'Tablet', 'Monitor'];
        $marcas = ['Dell', 'HP', 'Lenovo', 'Samsung', 'Apple', 'Acer', 'Asus'];

        return [
            'id' => self::$idCounter++,
            'cliente_id' => Cliente::factory(),
            'tipo' => $this->faker->randomElement($tipos),
            'marca' => $this->faker->randomElement($marcas),
            'modelo' => $this->faker->bothify('Model-###'),
            'numero_serie' => $this->faker->optional()->bothify('SN-########'),
        ];
    }
}
