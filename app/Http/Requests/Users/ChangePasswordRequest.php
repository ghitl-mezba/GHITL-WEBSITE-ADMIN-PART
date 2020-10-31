<?php

namespace App\Http\Requests\Users;

use App\Rules\MatchOldPassword;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    
    public function rules()
    {
        return [
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ];
    }
}
