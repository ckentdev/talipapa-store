<?php

namespace App\Rules;

use App\Support\PasswordStrength;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! PasswordStrength::isStrong($value)) {
            $fail('Use a strong password with uppercase, lowercase, a number, and a special character.');
        }
    }
}
