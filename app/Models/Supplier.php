<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document',
        'document_type',
        'name',
        'trade_name',
        'email',
        'phone',
        'mobile_phone',
        'zip_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'cnae_code',
        'cnae_description',
        'legal_nature',
        'opening_date',
        'situation',
        'notes',
        'active'
    ];

    protected $casts = [
        'opening_date' => 'date',
        'active' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // Accessor para formatar o documento
    public function getFormattedDocumentAttribute()
    {
        if ($this->document_type === 'cnpj') {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->document);
        } else {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->document);
        }
    }

    // Mutator para remover formatação do documento
    public function setDocumentAttribute($value)
    {
        $this->attributes['document'] = preg_replace('/[^0-9]/', '', $value);
    }

    // Scope para buscar por documento
    public function scopeByDocument($query, $document)
    {
        $cleanDocument = preg_replace('/[^0-9]/', '', $document);
        return $query->where('document', $cleanDocument);
    }

    // Scope para ativos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Verifica se é CNPJ
    public function isCnpj()
    {
        return $this->document_type === 'cnpj';
    }

    // Verifica se é CPF
    public function isCpf()
    {
        return $this->document_type === 'cpf';
    }
}
