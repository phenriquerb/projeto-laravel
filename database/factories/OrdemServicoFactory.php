<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\OrdemServico;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrdemServico>
 */
class OrdemServicoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = OrdemServico::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ano = now()->format('Y');
        $mes = now()->format('m');
        $numero = str_pad($this->faker->unique()->numberBetween(1, 9999), 3, '0', STR_PAD_LEFT);
        $protocolo = "{$ano}{$mes}-{$numero}";

        return [
            'protocolo' => $protocolo,
            'cliente_id' => Cliente::factory(),
            'equipamento_id' => function (array $attributes) {
                return Equipamento::factory()->create([
                    'cliente_id' => $attributes['cliente_id'],
                ])->id;
            },
            'atendente_id' => Funcionario::factory(),
            'relato_cliente' => $this->faker->paragraph(),
            'diagnostico_tecnico' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(['aberta', 'em_analise', 'aguardando_pecas', 'execucao', 'concluida', 'cancelada']),
            'prioridade' => $this->faker->randomElement(['baixa', 'media', 'alta', 'critica']),
            'valor_total' => $this->faker->randomFloat(2, 50, 5000),
            'data_conclusao' => $this->faker->optional()->dateTime(),
        ];
    }
}
