<?php

namespace App\Http\Requests;

use App\Enums\ReturnMessages;
use App\Http\Controllers\ApiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
{
    use ApiResponse;
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
            'scientific_name' => 'min:5|max:50',
            'commercial_name' => 'min:5|max:50',
            'company_name' => 'min:5|max:50',
            'quantity' => '',
            'price' => '',
            'category_id' => '',
            'warehouse_id' => '',
            'expiration' => 'date',
            // 'image' => ['image','mimes:jpg,jpeg,png,svg']
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(self::apiResponse(422,ReturnMessages::ValidateError->value,null,null,$validator->errors()));
    }
}
