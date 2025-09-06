<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Str;

class HtmlFilled implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Str::of($value)->stripTags()->trim()->isEmpty()) {
            $fail('validation.filled')->translate(['attribute' => $attribute]);
        }
    }
}
