<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessCorrectionRequest extends FormRequest
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
        return [
            'status' => ['required', \Illuminate\Validation\Rule::in([\App\Enums\RequestStatus::APPROVED->value, \App\Enums\RequestStatus::REJECTED->value])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
