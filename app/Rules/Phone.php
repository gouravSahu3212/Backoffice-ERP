<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Phone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $normalized = preg_replace('/[\s\-\(\)]+/', '', $value);

        $patterns = [
            'Saudi' => '/^(?:\+?966|0)?5\d{8}$/',
            'Jordon' => '/^(?:\+?962|0)?7[789]\d{7}$/',
            'Morocco' => '/^(?:\+?212|0)?[67]\d{8}$/',
            'Egypt' => '/^(?:\+?20|0)?1[0125]\d{8}$/',
            'Turkey' => '/^(?:\+?90|0)?5\d{9}$/',
            'UAE' => '/^(?:\+?971|0)?5[024568]\d{7}$/',
        ];

        foreach ($patterns as $country => $pattern) {
            if (preg_match($pattern, $normalized)) {
                return;
            }
        }

        $fail('The :attribute must be a valid phone number for Saudi, Jordon, Morocco, Egypt, Turkey, or UAE.');
    }
}
