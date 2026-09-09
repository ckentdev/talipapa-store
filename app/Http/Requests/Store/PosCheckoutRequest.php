<?php

namespace App\Http\Requests\Store;

use App\Enums\EWalletProvider;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PosCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStoreOwner() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $items = $this->input('items');

        if (is_string($items)) {
            $items = json_decode($items, true) ?? [];
        }

        $this->merge(['items' => is_array($items) ? $items : []]);

        if ($this->input('payment_method') === 'gcash') {
            $this->merge(['payment_method' => PaymentMethod::EWallet->value]);
        }

        if ($this->filled('card_number')) {
            $this->merge([
                'card_number' => preg_replace('/\D/', '', (string) $this->input('card_number')),
            ]);
        }

        if (! $this->filled('discount_type')) {
            $this->merge(['discount_type' => 'none']);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'payment_method' => ['required', new Enum(PaymentMethod::class)],
            'discount_type' => ['nullable', 'in:none,fixed,percent'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'cash_tendered' => [
                'nullable',
                'numeric',
                'min:0',
                'required_if:payment_method,'.PaymentMethod::Cash->value,
            ],
        ], match ($this->input('payment_method')) {
            PaymentMethod::EWallet->value => [
                'ewallet_provider' => ['required', new Enum(EWalletProvider::class)],
            ],
            PaymentMethod::Card->value => [
                'card_holder_name' => ['required', 'string', 'max:255'],
                'card_number' => ['required', 'digits_between:13,19'],
                'card_expiry' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
                'card_cvv' => ['required', 'string', 'regex:/^\d{3,4}$/'],
            ],
            default => [],
        });
    }
}
