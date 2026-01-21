<?php

namespace Database\Seeders;

use App\Enums\CargoEnum;
use App\Models\Cargo;
use Illuminate\Database\Seeder;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // IDs fixos para garantir relacionamentos previsíveis
        $cargos = [
            ['id' => CargoEnum::ATENDENTE->value, 'nome' => 'Atendente'],
            ['id' => CargoEnum::TECNICO->value, 'nome' => 'Tecnico'],
        ];

        foreach ($cargos as $cargo) {
            Cargo::create($cargo);
        }
    }
}
