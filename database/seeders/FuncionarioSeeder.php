<?php

namespace Database\Seeders;

use App\Models\Funcionario;
use Illuminate\Database\Seeder;

class FuncionarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $funcionarios = [
            ['id' => 1, 'nome' => 'João Silva', 'cargo_id' => 1],
            ['id' => 2, 'nome' => 'Maria Santos', 'cargo_id' => 2],
            ['id' => 3, 'nome' => 'Pedro Oliveira', 'cargo_id' => 1],
            ['id' => 4, 'nome' => 'Ana Costa', 'cargo_id' => 3],
            ['id' => 5, 'nome' => 'Carlos Pereira', 'cargo_id' => 4],
            ['id' => 6, 'nome' => 'Juliana Alves', 'cargo_id' => 2],
            ['id' => 7, 'nome' => 'Roberto Lima', 'cargo_id' => 5],
            ['id' => 8, 'nome' => 'Fernanda Souza', 'cargo_id' => 1],
        ];

        foreach ($funcionarios as $funcionario) {
            Funcionario::create($funcionario);
        }
    }
}
