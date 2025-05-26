<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Recipe extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'image' => 'nullable|image',
            'video' => 'nullable|url',
            'description' => 'required|string',
            'instructions' => 'required|string',
            'preparation_time' => 'required|integer',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity_2' => 'nullable|numeric',
            'ingredients.*.quantity_4' => 'nullable|numeric',
            'ingredients.*.quantity_8' => 'nullable|numeric',
        ];
    }
}
