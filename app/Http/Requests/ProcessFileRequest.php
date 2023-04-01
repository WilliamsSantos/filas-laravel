<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [ 
                'required', 
                Rule::exists('import_queue', 'filename')
            ],
        ];
    }

    public function messages()
    {
        return [
            'file.exists' => 'Arquivo não identificado.'        
        ];
    }
}
