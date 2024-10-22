<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEndOfRideRequest extends FormRequest
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
            'end_location' => ['required', 'string'],
            'end_lat' => ['required', 'numeric', 'between:-90,90'],
            'end_lng' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
