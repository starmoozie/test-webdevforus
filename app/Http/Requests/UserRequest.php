<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\User;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return starmoozie_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request()->id;
        return [
            'password' => 'confirmed',
            'role'     => 'required',
            'name'     => [
                'max:50',
                'required',
                'regex:/^[a-z A-Z]+$/'
            ],
            'email' => [
                'max:50',
                'required',
                'email',
                Rule::unique(User::class)->when($this->method() === 'PUT', fn($q) => $q->ignore($id))
            ],
            'mobile' => [
                'required',
                'regex:/(08)[0-9]{6,15}/',
                Rule::unique(User::class)->when($this->method() === 'PUT', fn($q) => $q->ignore($id))
            ],
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
