<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckRideStatusRequest extends FormRequest
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
            'ride_id' => ['sometimes', 'numeric'],
            'customer_id' => ['sometimes', 'numeric'],
            'driver_id' => ['sometimes', 'required_without:customer_id', 'numeric'],
        ];
    }
}
