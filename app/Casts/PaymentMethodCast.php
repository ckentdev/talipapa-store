<?php

namespace App\Casts;

use App\Enums\PaymentMethod;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): PaymentMethod
    {
        return PaymentMethod::tryFromStored(is_string($value) ? $value : null) ?? PaymentMethod::Cod;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof PaymentMethod) {
            return $value->value;
        }

        return PaymentMethod::tryFromStored(is_string($value) ? $value : null)?->value
            ?? PaymentMethod::Cod->value;
    }
}
