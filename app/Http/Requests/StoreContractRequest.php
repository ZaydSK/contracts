<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' =>'required|string|min:3',
            'executing_agency' =>'required|string|min:3',
            'watching_agency' =>'required|string|min:3',
            'date' =>'required|date',
            'number' =>'required|string',
            'branch' =>'required|string',
            'content' =>'required|string',
            'price' => 'required|numeric',
            'up_percent' => 'required|numeric',
            'down_percent' => 'required|numeric',
            'up_price' => 'required|numeric',
            'starting_date' => 'required|date|after:date',
            'finishing_date' => 'required|date|after:starting_date',
            'execution_period' => 'required|string',
            'materials' => 'required|array|min:1',
            'materials.*.material_name' => 'required|string',
            'materials.*.unit' => 'required|string',
            'materials.*.quantity' => 'required|numeric',
            'materials.*.individual_price' => 'required|numeric',
            'materials.*.overall_price' => 'required|numeric',
        ];
        
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'msg' => 'An error was occurred',
            'errors' => $validator->errors()->all(),
        ], 422));
    }
}
