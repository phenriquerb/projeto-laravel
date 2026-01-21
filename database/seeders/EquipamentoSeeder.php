<?php

namespace Database\Seeders;

use App\Models\Equipamento;
use Illuminate\Database\Seeder;

class EquipamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // IDs fixos para garantir relacionamentos previsíveis
        $equipamentos = [
            [
                'id' => 1,
                'cliente_id' => 1,
                'tipo' => 'Notebook',
                'marca' => 'Dell',
                'modelo' => 'Inspiron 15 3000',
                'numero_serie' => 'DL123456789',
            ],
            [
                'id' => 2,
                'cliente_id' => 1,
                'tipo' => 'Desktop',
                'marca' => 'HP',
                'modelo' => 'ProDesk 400 G7',
                'numero_serie' => 'HP987654321',
            ],
            [
                'id' => 3,
                'cliente_id' => 2,
                'tipo' => 'Notebook',
                'marca' => 'Lenovo',
                'modelo' => 'ThinkPad E14',
                'numero_serie' => 'LN456789123',
            ],
            [
                'id' => 4,
                'cliente_id' => 3,
                'tipo' => 'Smartphone',
                'marca' => 'Samsung',
                'modelo' => 'Galaxy S21',
                'numero_serie' => 'SM741258963',
            ],
            [
                'id' => 5,
                'cliente_id' => 3,
                'tipo' => 'Impressora',
                'marca' => 'Epson',
                'modelo' => 'L3150',
                'numero_serie' => 'EP159753486',
            ],
            [
                'id' => 6,
                'cliente_id' => 4,
                'tipo' => 'Notebook',
                'marca' => 'Acer',
                'modelo' => 'Aspire 5',
                'numero_serie' => 'AC852963741',
            ],
        ];

        foreach ($equipamentos as $equipamento) {
            Equipamento::create($equipamento);
        }
    }
}
