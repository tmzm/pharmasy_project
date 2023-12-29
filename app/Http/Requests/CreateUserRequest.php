<?php

namespace App\Http\Requests;

use App\Enums\ReturnMessages;
use App\Http\Controllers\ApiResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateUserRequest extends FormRequest
{
    use ApiResponse;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Request $request): bool
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
        if(request('role') === 'user')
            return [
                'name' => 'required|min:3|max:50',
                'phone_number' => 'required|unique:users,phone_number',
                'password' => 'required|min:8|max:30',
                'role' => 'required'
            ];
        else
            return [
                'name' => 'required|min:3|max:50',
                'phone_number' => 'required|unique:users,phone_number',
                'password' => 'required|min:8|max:30',
                'role' => 'required',
                'warehouseName' => 'required|min:3|max:50',
                'image' => ['required','image','mimes:jpg,jpeg,png,svg'],
                'location' => 'required|min:10|max:50',
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
