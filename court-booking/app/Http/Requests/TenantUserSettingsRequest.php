<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantUserSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        if ($this->has('current_password')) {
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ];
        }

        return $rules;
    }
} 