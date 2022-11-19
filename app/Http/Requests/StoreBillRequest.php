<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules():array
    {
        return [
            'date' => 'required|date',
            'discount' => 'required|numeric',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|numeric',
            'materials.*.quantity' => 'required|numeric',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'msg' => 'An error was occurred',
            'error' => $validator->errors()->all()[0],
        ], 400));
    }
}
