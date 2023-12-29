<?php

namespace App\Http\Requests;

use App\Enums\ReturnMessages;
use App\Http\Controllers\ApiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
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
        if(request()->user()->role === 'user')
            return [
                'name' => 'min:3|max:50',
                'phone_number' => 'unique:users,phone_number',
            ];
        else
            return [
                'name' => 'min:3|max:50',
                'phone_number' => 'unique:users,phone_number',
                'warehouseName' => 'min:3|max:50',
                'location' => 'min:10|max:50',
                'image' => ['image','mimes:jpg,jpeg,png,svg'],
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
