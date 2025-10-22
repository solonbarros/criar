<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request validation for registering access entries.
 */
class StoreAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('access.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'visitor_id' => ['required', 'exists:visitors,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'purpose' => ['required', 'string', 'max:255'],
        ];
    }
}
