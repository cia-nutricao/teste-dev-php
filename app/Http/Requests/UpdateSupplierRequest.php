<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\BrasilApiService;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $supplierId = $this->route('supplier');
        
        return [
            'document' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('suppliers', 'document')->ignore($supplierId),
                function ($attribute, $value, $fail) {
                    $brasilApi = new BrasilApiService();
                    $cleanDocument = preg_replace('/[^0-9]/', '', $value);
                    
                    if (strlen($cleanDocument) === 14) {
                        // CNPJ
                        if (!$brasilApi->validateCnpj($cleanDocument)) {
                            $fail('The CNPJ is invalid.');
                        }
                    } elseif (strlen($cleanDocument) === 11) {
                        // CPF
                        if (!$brasilApi->validateCpf($cleanDocument)) {
                            $fail('The CPF is invalid.');
                        }
                    } else {
                        $fail('The document must be a valid CNPJ or CPF.');
                    }
                }
            ],
            'document_type' => [
                'sometimes',
                'required',
                'in:cnpj,cpf',
                function ($attribute, $value, $fail) {
                    $document = preg_replace('/[^0-9]/', '', $this->input('document', ''));
                    
                    if ($document && $value === 'cnpj' && strlen($document) !== 14) {
                        $fail('Document type CNPJ requires a 14-digit document.');
                    }
                    
                    if ($document && $value === 'cpf' && strlen($document) !== 11) {
                        $fail('Document type CPF requires an 11-digit document.');
                    }
                }
            ],
            'name' => 'sometimes|required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile_phone' => 'nullable|string|max:20',
            'zip_code' => 'nullable|string|size:8',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|size:2',
            'cnae_code' => 'nullable|string|max:10',
            'cnae_description' => 'nullable|string|max:500',
            'legal_nature' => 'nullable|in:MEI,LTDA,SA,EIRELI,OTHER',
            'opening_date' => 'nullable|date',
            'situation' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'active' => 'boolean'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'document.required' => 'The document field is required.',
            'document.unique' => 'This document is already registered.',
            'document_type.required' => 'The document type field is required.',
            'document_type.in' => 'The document type must be either CNPJ or CPF.',
            'name.required' => 'The name field is required.',
            'email.email' => 'The email must be a valid email address.',
            'zip_code.size' => 'The ZIP code must be exactly 8 digits.',
            'state.size' => 'The state must be exactly 2 characters.',
            'legal_nature.in' => 'The legal nature must be one of: MEI, LTDA, SA, EIRELI, OTHER.',
            'opening_date.date' => 'The opening date must be a valid date.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $data = [];
        
        if ($this->has('document')) {
            $data['document'] = preg_replace('/[^0-9]/', '', $this->document);
        }
        
        if ($this->has('zip_code')) {
            $data['zip_code'] = preg_replace('/[^0-9]/', '', $this->zip_code);
        }
        
        if ($this->has('active')) {
            $data['active'] = $this->boolean('active');
        }
        
        $this->merge($data);
    }
}
