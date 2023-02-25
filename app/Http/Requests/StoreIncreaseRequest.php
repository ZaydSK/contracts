<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreIncreaseRequest extends FormRequest
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
            'number' => ['required','string'],
            'period' => ['required','string'],
            'date' => ['required','date'],
            'doc' => 'file|mimes:png,jpg,pdf',
            'materials' => ['required','array','min:1'],
            'materials.*.id' => [ 'required','numeric', 
                Rule::exists('contract_materials','id')->where('contract_id',$this->contract->id)],
            'materials.*.percent' => ['required','numeric','max:30'],
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
