<?php

namespace Database\Seeders;

use App\Enums\CargoEnum;
use App\Models\Funcionario;
use Illuminate\Database\Seeder;

class FuncionarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // IDs fixos para garantir relacionamentos previsíveis
        $funcionarios = [
            ['id' => 1, 'nome' => 'João Silva', 'email' => 'joao.silva@example.com', 'login' => 'joao.silva', 'password' => bcrypt('password'), 'cargo_id' => CargoEnum::ATENDENTE->value],
            ['id' => 2, 'nome' => 'Maria Santos', 'email' => 'maria.santos@example.com', 'login' => 'maria.santos', 'password' => bcrypt('password'), 'cargo_id' => CargoEnum::TECNICO->value],
            ['id' => 3, 'nome' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@example.com', 'login' => 'pedro.oliveira', 'password' => bcrypt('password'), 'cargo_id' => CargoEnum::TECNICO->value],
            ['id' => 4, 'nome' => 'Ana Costa', 'email' => 'ana.costa@example.com', 'login' => 'ana.costa', 'password' => bcrypt('password'), 'cargo_id' => CargoEnum::ATENDENTE->value],
            ['id' => 5, 'nome' => 'Carlos Pereira', 'email' => 'carlos.pereira@example.com', 'login' => 'carlos.pereira', 'password' => bcrypt('password'), 'cargo_id' => CargoEnum::ATENDENTE->value],
            ['id' => 6, 'nome' => 'Juliana Alves', 'email' => 'juliana.alves@example.com', 'login' => 'juliana.alves', 'password' => bcrypt('password'), 'cargo_id' => CargoEnum::TECNICO->value],
            ['id' => 7, 'nome' => 'Roberto Lima', 'email' => 'roberto.lima@example.com', 'login' => 'roberto.lima', 'password' => bcrypt('password'), 'cargo_id' => CargoEnum::TECNICO->value],
            ['id' => 8, 'nome' => 'Fernanda Souza', 'email' => 'fernanda.souza@example.com', 'login' => 'fernanda.souza', 'password' => bcrypt('password'), 'cargo_id' => CargoEnum::ATENDENTE->value],
        ];

        foreach ($funcionarios as $funcionario) {
            Funcionario::create($funcionario);
        }
    }
}
