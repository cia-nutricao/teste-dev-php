<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentType = $this->faker->randomElement(['cnpj', 'cpf']);
        
        return [
            'document' => $documentType === 'cnpj' ? $this->generateCnpj() : $this->generateCpf(),
            'document_type' => $documentType,
            'name' => $documentType === 'cnpj' ? $this->faker->company : $this->faker->name,
            'trade_name' => $documentType === 'cnpj' ? $this->faker->optional()->companySuffix : null,
            'email' => $this->faker->optional()->email,
            'phone' => $this->faker->optional()->phoneNumber,
            'mobile_phone' => $this->faker->optional()->phoneNumber,
            'zip_code' => $this->faker->optional()->numerify('########'),
            'street' => $this->faker->optional()->streetName,
            'number' => $this->faker->optional()->buildingNumber,
            'complement' => $this->faker->optional()->secondaryAddress,
            'neighborhood' => $this->faker->optional()->citySuffix,
            'city' => $this->faker->optional()->city,
            'state' => $this->faker->optional()->stateAbbr,
            'cnae_code' => $documentType === 'cnpj' ? $this->faker->optional()->numerify('#######') : null,
            'cnae_description' => $documentType === 'cnpj' ? $this->faker->optional()->jobTitle : null,
            'legal_nature' => $documentType === 'cnpj' ? $this->faker->optional()->randomElement(['MEI', 'LTDA', 'SA', 'EIRELI', 'OTHER']) : null,
            'opening_date' => $documentType === 'cnpj' ? $this->faker->optional()->date : null,
            'situation' => $documentType === 'cnpj' ? $this->faker->optional()->randomElement(['ATIVA', 'SUSPENSA', 'BAIXADA']) : null,
            'notes' => $this->faker->optional()->text,
            'active' => $this->faker->boolean(90), // 90% chance de estar ativo
        ];
    }

    /**
     * Generate a valid CNPJ for testing
     */
    private function generateCnpj(): string
    {
        // Gera os primeiros 12 dígitos
        $cnpj = '';
        for ($i = 0; $i < 12; $i++) {
            $cnpj .= mt_rand(0, 9);
        }

        // Calcula primeiro dígito verificador
        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;
        $cnpj .= $digit1;

        // Calcula segundo dígito verificador
        $sum = 0;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;
        $cnpj .= $digit2;

        return $cnpj;
    }

    /**
     * Generate a valid CPF for testing
     */
    private function generateCpf(): string
    {
        // Gera os primeiros 9 dígitos
        $cpf = '';
        for ($i = 0; $i < 9; $i++) {
            $cpf .= mt_rand(0, 9);
        }

        // Calcula primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;
        $cpf .= $digit1;

        // Calcula segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;
        $cpf .= $digit2;

        return $cpf;
    }

    /**
     * Create a supplier with CNPJ
     */
    public function cnpj(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'document' => $this->generateCnpj(),
                'document_type' => 'cnpj',
                'name' => $this->faker->company,
                'trade_name' => $this->faker->companySuffix,
                'cnae_code' => $this->faker->numerify('#######'),
                'cnae_description' => $this->faker->jobTitle,
                'legal_nature' => $this->faker->randomElement(['MEI', 'LTDA', 'SA', 'EIRELI', 'OTHER']),
                'opening_date' => $this->faker->date,
                'situation' => $this->faker->randomElement(['ATIVA', 'SUSPENSA', 'BAIXADA']),
            ];
        });
    }

    /**
     * Create a supplier with CPF
     */
    public function cpf(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'document' => $this->generateCpf(),
                'document_type' => 'cpf',
                'name' => $this->faker->name,
                'trade_name' => null,
                'cnae_code' => null,
                'cnae_description' => null,
                'legal_nature' => null,
                'opening_date' => null,
                'situation' => null,
            ];
        });
    }

    /**
     * Create an active supplier
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => true,
            ];
        });
    }

    /**
     * Create an inactive supplier
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }
}
