<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'nip'        => 'required',
            'name'       => 'required',
            'address'    => 'present',
            'address2'   => 'present',
            'zip'        => 'present',
            'city'       => 'present',
            'phone'      => 'present',
            'mail'       => 'nullable|email',
            'type'       => 'numeric',
            'id_company' => 'numeric'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'id_company' => 1
        ]);
    }
}
