<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin === 1;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['required', 'string', 'min:10'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'La catégorie est requise.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'name.required' => 'Le nom du produit est requis.',
            'name.unique' => 'Ce nom de produit est déjà utilisé.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'description.required' => 'La description est requise.',
            'description.min' => 'La description doit contenir au moins 10 caractères.',
            'price.required' => 'Le prix est requis.',
            'price.min' => 'Le prix doit être supérieur à 0.',
            'stock.required' => 'Le stock est requis.',
            'price.numeric' => 'Le prix doit être un nombre valide.',
            'stock.integer' => 'Le stock doit être un nombre entier.',
        ];
    }
}
