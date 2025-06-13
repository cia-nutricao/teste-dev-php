<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class BrasilApiService
{
    protected $baseUrl = 'https://brasilapi.com.br/api';

    public function getCnpjData(string $cnpj): ?array
    {
        $cleanCnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        if (strlen($cleanCnpj) !== 14) {
            return null;
        }

        // Cache por 24 horas
        $cacheKey = "cnpj_data_{$cleanCnpj}";
        
        return Cache::remember($cacheKey, 86400, function () use ($cleanCnpj) {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}/cnpj/v1/{$cleanCnpj}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatCnpjData($data);
                }
                
                return null;
            } catch (Exception $e) {
                Log::error('BrasilAPI Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    protected function formatCnpjData(array $data): array
    {
        return [
            'document' => $data['cnpj'] ?? null,
            'document_type' => 'cnpj',
            'name' => $data['razao_social'] ?? null,
            'trade_name' => $data['nome_fantasia'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['telefone'] ?? null,
            'zip_code' => $data['cep'] ?? null,
            'street' => $data['logradouro'] ?? null,
            'number' => $data['numero'] ?? null,
            'complement' => $data['complemento'] ?? null,
            'neighborhood' => $data['bairro'] ?? null,
            'city' => $data['municipio'] ?? null,
            'state' => $data['uf'] ?? null,
            'cnae_code' => $data['cnae_fiscal'] ?? null,
            'cnae_description' => $data['cnae_fiscal_descricao'] ?? null,
            'legal_nature' => $this->mapLegalNature($data['natureza_juridica'] ?? null),
            'opening_date' => $data['data_inicio_atividade'] ? date('Y-m-d', strtotime($data['data_inicio_atividade'])) : null,
            'situation' => $data['situacao'] ?? null,
        ];
    }

    protected function mapLegalNature(?string $naturezaJuridica): ?string
    {
        if (!$naturezaJuridica) {
            return null;
        }

        $mapping = [
            'Microempreendedor Individual' => 'MEI',
            'Sociedade Limitada' => 'LTDA',
            'Sociedade Anônima' => 'SA',
            'Empresa Individual de Responsabilidade Limitada' => 'EIRELI',
        ];

        foreach ($mapping as $key => $value) {
            if (stripos($naturezaJuridica, $key) !== false) {
                return $value;
            }
        }

        return 'OTHER';
    }

    public function validateCnpj(string $cnpj): bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Calcula primeiro dígito verificador
        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;
        
        if ($cnpj[12] != $digit1) {
            return false;
        }

        // Calcula segundo dígito verificador
        $sum = 0;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;
        
        return $cnpj[13] == $digit2;
    }

    public function validateCpf(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;
        
        if ($cpf[9] != $digit1) {
            return false;
        }

        // Calcula segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;
        
        return $cpf[10] == $digit2;
    }
}