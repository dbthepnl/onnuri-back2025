<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UserRequest extends FormRequest
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
        Log::info('UserRequest Access');

        $results = [
            'username' => 'nullable|string|max:16|unique:users',
            'phone' => 'sometimes|numeric|digits_between:10,11',
            'name' => 'sometimes|max:255',
            'birth' => 'sometimes|numeric|digits:8',
            'birth_type' => 'sometimes',
            'address' => 'sometimes|max:255',
            'detail_address' => 'sometimes|max:255',
            'zip_code' => 'sometimese|numeric|digits:5',
            'email' => 'sometimes|string|email|max:255|unique:users',
            'homepage' => 'sometimes|max:255',
            'officers' => 'nullable|numeric',
            //'password' => 'sometimes|min:6|confirmed'
        ];

        if ($this->method() === 'PUT') {

        }

        return $results;
    }

    public function prepareForValidation()
    {
        //$input = $this->input();
        /*
        $board = $this->segments()[2];
        if (in_array($board, Post::BOARDS)) {
            $this->merge(['board' => $board]);
        }
        */
    }

    
}
