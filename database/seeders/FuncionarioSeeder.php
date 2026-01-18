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
            ['id' => 1, 'nome' => 'João Silva', 'email' => 'joao.silva@example.com', 'ativo' => true, 'cargo_id' => 1],
            ['id' => 2, 'nome' => 'Maria Santos', 'email' => 'maria.santos@example.com', 'ativo' => true, 'cargo_id' => 2],
            ['id' => 3, 'nome' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@example.com', 'ativo' => true, 'cargo_id' => 1],
            ['id' => 4, 'nome' => 'Ana Costa', 'email' => 'ana.costa@example.com', 'ativo' => true, 'cargo_id' => 3],
            ['id' => 5, 'nome' => 'Carlos Pereira', 'email' => 'carlos.pereira@example.com', 'ativo' => true, 'cargo_id' => 4],
            ['id' => 6, 'nome' => 'Juliana Alves', 'email' => 'juliana.alves@example.com', 'ativo' => true, 'cargo_id' => 2],
            ['id' => 7, 'nome' => 'Roberto Lima', 'email' => 'roberto.lima@example.com', 'ativo' => false, 'cargo_id' => 5],
            ['id' => 8, 'nome' => 'Fernanda Souza', 'email' => 'fernanda.souza@example.com', 'ativo' => true, 'cargo_id' => 1],
        ];

        foreach ($funcionarios as $funcionario) {
            Funcionario::create($funcionario);
        }
    }
}
