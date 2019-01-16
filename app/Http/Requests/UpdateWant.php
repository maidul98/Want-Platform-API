<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Want;

class UpdateWant extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $want = Want::findOrFail($this->route('id'));
        return Auth::user()->id == $want->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'description' => 'max:300',
            'cost' => 'required|numeric|digits_between:0,9999',
            'category' => 'exists:categories,id|required|numeric'
        ];
    }
}
