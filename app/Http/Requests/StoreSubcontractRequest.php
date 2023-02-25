<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSubcontractRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'number' =>'required|string',
            'doc' => 'file|mimes:png,jpg,pdf',
            'starting_date' => 'required|date|after:date',
            'subject' => 'required|string',
            'period' => 'required|string',
            'other_materials' => 'array',
            'other_materials.*.material_name' => 'required|string',
            'other_materials.*.unit' => 'required|string',
            'other_materials.*.quantity' => 'required|numeric',
            'other_materials.*.individual_price' => 'required|numeric',
            'other_materials.*.overall_price' => 'required|numeric',
            'other_materials.*.number' => 'required|string',
            'contract_materials' =>'array',
            'contract_materials.*.id' => 'required|numeric|exists:contract_materials',
            'contract_materials.*.quantity' => 'required|numeric'
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
