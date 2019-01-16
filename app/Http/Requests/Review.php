<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Review extends FormRequest
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
            'rating' => 'in:1,2,3,4,5|numeric|required',
            'feedback' => 'string|max:4776',
            'want_id' => 'required|numeric',
            'fulfiller_id' => 'required|numeric' 
        ];
    }
}
