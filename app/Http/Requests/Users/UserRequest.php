<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    
    public function authorize()
    {
        return true;
    }

    
    public function rules()
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        } elseif ($this->isMethod('put')) {
            return $this->updateRules();
        }
    }

    public function createRules(): array
    {
        return [
            'type' => 'required|in:admin,user',
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6'
        ];
    }

   
    public function updateRules(): array
    {
        return [
            'type' => 'sometimes|in:admin,user',
            'name' => 'sometimes|string|max:191',
            'email' => 'sometimes|string|email|max:191|unique:users,email,' . $this->get('id')
        ];
    }
}
