<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
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
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'driving_license_category' => ['required', 'string', Rule::in(['B', 'BE'])],
            'driving_license_number' => ['required', 'numeric', 'between:100000,999999'],
            'numbers' => ['sometimes', 'array'],
            'numbers.*.id' => ['sometimes', 'numeric'],
            'numbers.*.number' => ['required', 'numeric'],
        ];
    }
}
