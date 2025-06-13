<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar alguns fornecedores de exemplo com CNPJ
        Supplier::factory()->cnpj()->create([
            'name' => 'Tech Solutions LTDA',
            'trade_name' => 'TechSol',
            'email' => 'contato@techsol.com.br',
            'phone' => '1134567890',
            'city' => 'São Paulo',
            'state' => 'SP',
            'active' => true
        ]);

        Supplier::factory()->cnpj()->create([
            'name' => 'Distribuidora ABC S.A.',
            'trade_name' => 'ABC Dist',
            'email' => 'vendas@abcdist.com.br',
            'phone' => '2133445566',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'active' => true
        ]);

        // Criar alguns fornecedores pessoa física com CPF
        Supplier::factory()->cpf()->create([
            'name' => 'Maria Silva Santos',
            'email' => 'maria.santos@email.com',
            'phone' => '11987654321',
            'city' => 'São Paulo',
            'state' => 'SP',
            'active' => true
        ]);

        Supplier::factory()->cpf()->create([
            'name' => 'João Carlos Oliveira',
            'email' => 'joao.oliveira@email.com',
            'phone' => '21976543210',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'active' => true
        ]);

        // Criar mais fornecedores aleatórios
        Supplier::factory()->count(15)->create();
    }
}
