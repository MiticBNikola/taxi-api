<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexRideRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', 'numeric'],
            'driver_id' => ['sometimes', 'numeric'],
            'requested' => ['prohibited_if:customer_id,driver_id', 'boolean'],
            'search' => ['sometimes', 'prohibited_if:customer_id,driver_id', 'string'],
            'in_progress' => ['prohibited_if:customer_id,driver_id', 'boolean'],
            'page' => ['required', 'numeric'],
            'per_page' => ['required', 'numeric'],
        ];
    }
}
