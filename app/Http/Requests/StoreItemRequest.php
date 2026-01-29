<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
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
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'unit_id' => 'required|integer|exists:units,id',
            'item_name' => 'required|string|max:50|unique:items',
            'item_price' => 'required|numeric',
            'item_discount' => 'nullable|numeric',
            'making_duration' => 'required|numeric',
            'making_duration_unit' => 'required|in:Hour,Minutes',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg',
            'status' => 'required|in:Active,Inactive',
        ];
    }
}
