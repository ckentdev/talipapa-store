<?php

namespace App\Rules;

use App\Support\PhilippinePhone;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhilippinePhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! PhilippinePhone::isValid(is_string($value) ? $value : null)) {
            $fail('Enter a valid Philippine mobile number starting with 9 (e.g. 917 123 4567).');
        }
    }
}
