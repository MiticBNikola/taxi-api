<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRideRequest extends FormRequest
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
            'start_location' => ['required', 'string'],
            'start_lat' => ['required', 'numeric', 'between:-90,90'],
            'start_lng' => ['required', 'numeric', 'between:-180,180'],
            'end_location' => ['sometimes', 'string'],
            'end_lat' => ['sometimes', 'numeric', 'between:-90,90'],
            'end_lng' => ['sometimes', 'numeric', 'between:-180,180'],
            'customer_id' => ['sometimes', 'numeric'],
            'request_time' => ['required', 'string'],
        ];
    }
}
