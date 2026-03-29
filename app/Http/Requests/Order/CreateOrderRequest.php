<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'string', 'min:10'],
            'billing_address' => ['nullable', 'string', 'min:10'],
            'payment_method' => ['required', 'string', 'in:credit_card,paypal,stripe,bank_transfer'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'shipping_address.required' => 'L\'adresse de livraison est requise.',
            'shipping_address.min' => 'L\'adresse de livraison doit contenir au moins 10 caractères.',
            'payment_method.required' => 'La méthode de paiement est requise.',
            'payment_method.in' => 'La méthode de paiement sélectionnée est invalide.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 500 caractères.',
        ];
    }
}
