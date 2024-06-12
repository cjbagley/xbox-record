<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListPageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['string', 'nullable', 'max:30'],
            'page' => ['numeric'],
        ];
    }
}