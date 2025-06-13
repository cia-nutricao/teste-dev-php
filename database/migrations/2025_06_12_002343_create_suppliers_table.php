<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('document')->unique(); // CNPJ ou CPF
            $table->enum('document_type', ['cnpj', 'cpf']);
            $table->string('name'); // Nome da empresa ou pessoa
            $table->string('trade_name')->nullable(); // Nome fantasia (apenas para CNPJ)
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile_phone')->nullable();
            
            // Endereço
            $table->string('zip_code', 8)->nullable();
            $table->string('street')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            
            // Dados específicos para CNPJ
            $table->string('cnae_code')->nullable();
            $table->string('cnae_description')->nullable();
            $table->enum('legal_nature', ['MEI', 'LTDA', 'SA', 'EIRELI', 'OTHER'])->nullable();
            $table->date('opening_date')->nullable();
            $table->string('situation')->nullable();
            
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['document_type', 'document']);
            $table->index(['name']);
            $table->index(['active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
