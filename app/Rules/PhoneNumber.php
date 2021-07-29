<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumber implements Rule {
    public function passes($attribute, $value) {
        return (strlen($value) === 10 && ctype_digit($value) && $value[0] === '0' && in_array($value[1], [5, 6, 7]));
    }

    public function message() {
        return 'The phone number is invalid.';
    }
}
