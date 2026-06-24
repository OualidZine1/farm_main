<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCin implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // A standard CIN format: 1-2 letters followed by 5-8 digits
        if (!preg_match('/^[A-Z]{1,2}\d{5,8}$/', strtoupper($value))) {
            $fail('The :attribute must be a valid Moroccan CIN (e.g., AB123456).');
        }
    }
}
