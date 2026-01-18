<?php

namespace Database\Seeders;

use App\Models\Cargo;
use Illuminate\Database\Seeder;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cargos = [
            ['id' => 1, 'nome' => 'Desenvolvedor'],
            ['id' => 2, 'nome' => 'Analista'],
            ['id' => 3, 'nome' => 'Gerente'],
            ['id' => 4, 'nome' => 'Designer'],
            ['id' => 5, 'nome' => 'QA'],
        ];

        foreach ($cargos as $cargo) {
            Cargo::create($cargo);
        }
    }
}
