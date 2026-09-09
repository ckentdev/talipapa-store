<?php

namespace App\Http\Requests\Checkout;

use App\Enums\EWalletProvider;
use App\Enums\PaymentMethod;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class ProcessCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('payment_method') === 'gcash') {
            $this->merge(['payment_method' => PaymentMethod::EWallet->value]);
        }

        if ($this->filled('card_number')) {
            $this->merge([
                'card_number' => preg_replace('/\D/', '', (string) $this->input('card_number')),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $step = (int) $this->input('step', 1);

        return match ($step) {
            1 => [],
            2 => [
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
            ],
            3 => [
                'address_id' => [
                    'required',
                    Rule::exists('addresses', 'id')->where('user_id', $this->user()->id),
                ],
                'notes' => ['nullable', 'string', 'max:500'],
            ],
            4 => array_merge([
                'payment_method' => [
                    'required',
                    Rule::in(array_map(
                        fn (PaymentMethod $method) => $method->value,
                        PaymentMethod::forOnlineCheckout(),
                    )),
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
            }),
            5 => [
                'confirm' => ['accepted'],
            ],
            default => [],
        };
    }

    protected function failedValidation(Validator $validator): void
    {
        $step = max(1, min(5, (int) $this->input('step', 1)));

        throw (new ValidationException($validator))
            ->redirectTo(route('customer.checkout.show', ['step' => $step]));
    }
}
