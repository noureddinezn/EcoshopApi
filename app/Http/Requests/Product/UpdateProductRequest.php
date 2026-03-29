<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255', 'unique:products,name,' . $productId],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:products,slug,' . $productId],
            'description' => ['sometimes', 'string', 'min:10'],
            'price' => ['sometimes', 'numeric', 'min:0.01'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'name.unique' => 'Ce nom de produit est déjà utilisé.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
            'description.min' => 'La description doit contenir au moins 10 caractères.',
            'price.numeric' => 'Le prix doit être un nombre valide.',
            'price.min' => 'Le prix doit être supérieur à 0.',
            'stock.integer' => 'Le stock doit être un nombre entier.',
        ];
    }
}
