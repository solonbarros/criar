<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request validation for updating visitors.
 */
class UpdateVisitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('visitors.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['sometimes', 'string', 'max:255'],
            'document_number' => ['sometimes', 'string', 'max:50', 'unique:visitors,document_number,' . $this->route('visitor')->id],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo_base64' => ['nullable', 'string'],
        ];
    }
}
