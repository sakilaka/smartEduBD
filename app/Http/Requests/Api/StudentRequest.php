<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
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
            'name_en'           => 'required',
            'name_bn'           => 'nullable',
            'mobile'            => 'numeric',
            'email'             => 'nullable',
            'profile.gender'    => 'required',
            'profile.religion'  => 'required',
            'profile.disability' => 'required',
            'profile.dob'       => 'nullable',
            'profile.nid_or_birth_reg'  => 'nullable',
            'profile.present_address'   => 'nullable',
            'profile.permanent_address' => 'nullable',
            'profile.fathers_name_en'   => 'required',
            'profile.fathers_name_bn'   => 'nullable',
            'profile.fathers_nid_or_birth_reg' => 'nullable',
            'profile.mothers_nid_or_birth_reg' => 'nullable',
            'profile.mothers_name_en' => 'required',
            'profile.mothers_name_bn' => 'nullable',
            'image'             => 'nullable|image|mimes:jpg,png,jpeg|mimetypes:image/jpeg,image/png',

        ];
    }

    protected function getValidatorInstance()
    {
        $json = $this->request->get('data');
        if (!empty($json)) {
            $this->merge(json_decode($json, true));
        }

        return parent::getValidatorInstance();
    }
}
