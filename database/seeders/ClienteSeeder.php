<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // IDs fixos para garantir relacionamentos previsíveis
        $clientes = [
            [
                'id' => 1,
                'nome' => 'Tech Solutions Ltda',
                'email' => 'contato@techsolutions.com.br',
                'cpf_cnpj' => '12.345.678/0001-90',
                'whatsapp' => '11987654321',
            ],
            [
                'id' => 2,
                'nome' => 'João Pedro da Silva',
                'email' => 'joao.pedro@email.com',
                'cpf_cnpj' => '123.456.789-00',
                'whatsapp' => '11912345678',
            ],
            [
                'id' => 3,
                'nome' => 'Maria Oliveira Comércio ME',
                'email' => 'maria@comercio.com.br',
                'cpf_cnpj' => '98.765.432/0001-10',
                'whatsapp' => '11998765432',
            ],
            [
                'id' => 4,
                'nome' => 'Carlos Eduardo Santos',
                'email' => 'carlos.santos@email.com',
                'cpf_cnpj' => '987.654.321-00',
                'whatsapp' => '11955443322',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
