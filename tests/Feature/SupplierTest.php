<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Supplier;
use App\Services\BrasilApiService;

class SupplierTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function it_can_list_suppliers()
    {
        Supplier::factory()->count(3)->create();

        $response = $this->getJson('/api/suppliers');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'document',
                            'document_formatted',
                            'document_type',
                            'name',
                            'email',
                            'address',
                            'active'
                        ]
                    ],
                    'meta',
                    'message'
                ]);
    }

    /** @test */
    public function it_can_create_a_supplier_with_cnpj()
    {
        $supplierData = [
            'document' => '11222333000181',
            'document_type' => 'cnpj',
            'name' => 'Empresa Teste LTDA',
            'trade_name' => 'Teste',
            'email' => 'contato@teste.com',
            'phone' => '11999999999',
            'city' => 'São Paulo',
            'state' => 'SP',
            'active' => true
        ];

        $response = $this->postJson('/api/suppliers', $supplierData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'document',
                        'document_type',
                        'name',
                        'trade_name'
                    ],
                    'message'
                ]);

        $this->assertDatabaseHas('suppliers', [
            'document' => '11222333000181',
            'document_type' => 'cnpj',
            'name' => 'Empresa Teste LTDA'
        ]);
    }

    /** @test */
    public function it_can_create_a_supplier_with_cpf()
    {
        $supplierData = [
            'document' => '11144477735', // CPF válido
            'document_type' => 'cpf',
            'name' => 'João Silva',
            'email' => 'joao@teste.com',
            'phone' => '11888888888',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
            'active' => true
        ];

        $response = $this->postJson('/api/suppliers', $supplierData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('suppliers', [
            'document' => '11144477735',
            'document_type' => 'cpf',
            'name' => 'João Silva'
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/suppliers', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['document', 'document_type', 'name']);
    }

    /** @test */
    public function it_validates_document_uniqueness()
    {
        $supplier = Supplier::factory()->create([
            'document' => '11222333000181'
        ]);

        $response = $this->postJson('/api/suppliers', [
            'document' => '11222333000181',
            'document_type' => 'cnpj',
            'name' => 'Outra Empresa'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['document']);
    }

    /** @test */
    public function it_can_show_a_supplier()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->getJson("/api/suppliers/{$supplier->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'document',
                        'document_type',
                        'name',
                        'address'
                    ],
                    'message'
                ]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_supplier()
    {
        $response = $this->getJson('/api/suppliers/999');

        $response->assertStatus(404)
                ->assertJson(['message' => 'Supplier not found']);
    }

    /** @test */
    public function it_can_update_a_supplier()
    {
        $supplier = Supplier::factory()->create();

        $updateData = [
            'name' => 'Nome Atualizado',
            'email' => 'novo@email.com'
        ];

        $response = $this->putJson("/api/suppliers/{$supplier->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Nome Atualizado',
            'email' => 'novo@email.com'
        ]);
    }

    /** @test */
    public function it_can_delete_a_supplier()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->deleteJson("/api/suppliers/{$supplier->id}");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Supplier deleted successfully']);

        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }

    /** @test */
    public function it_can_search_suppliers()
    {
        $supplier1 = Supplier::factory()->create(['name' => 'Empresa ABC']);
        $supplier2 = Supplier::factory()->create(['name' => 'Empresa XYZ']);

        $response = $this->getJson('/api/suppliers?search=ABC');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($supplier1->id, $data[0]['id']);
    }

    /** @test */
    public function it_can_filter_suppliers_by_document_type()
    {
        Supplier::factory()->create(['document_type' => 'cnpj']);
        Supplier::factory()->create(['document_type' => 'cpf']);

        $response = $this->getJson('/api/suppliers?document_type=cnpj');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('cnpj', $data[0]['document_type']);
    }

    /** @test */
    public function it_can_find_supplier_by_document()
    {
        $supplier = Supplier::factory()->create([
            'document' => '11222333000181'
        ]);

        $response = $this->postJson('/api/suppliers/find-by-document', [
            'document' => '11.222.333/0001-81'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => ['id' => $supplier->id],
                    'message' => 'Supplier found'
                ]);
    }

    /** @test */
    public function it_returns_404_when_document_not_found()
    {
        $response = $this->postJson('/api/suppliers/find-by-document', [
            'document' => '99999999999999'
        ]);

        $response->assertStatus(404)
                ->assertJson(['message' => 'Supplier not found']);
    }

    /** @test */
    public function it_validates_cnpj_format()
    {
        $response = $this->postJson('/api/suppliers/cnpj-data', [
            'cnpj' => '123'
        ]);

        $response->assertStatus(422)
                ->assertJson(['message' => 'Invalid CNPJ format']);
    }

    /** @test */
    public function it_can_paginate_suppliers()
    {
        Supplier::factory()->count(20)->active()->create();

        $response = $this->getJson('/api/suppliers?per_page=5');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ]);

        $meta = $response->json('meta');
        $this->assertEquals(5, $meta['per_page']);
        $this->assertEquals(20, $meta['total']);
    }

    /** @test */
    public function it_validates_document_type_consistency()
    {
        $response = $this->postJson('/api/suppliers', [
            'document' => '11222333000181', // 14 dígitos (CNPJ)
            'document_type' => 'cpf', // Tipo incorreto
            'name' => 'Teste'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['document_type']);
    }
}
