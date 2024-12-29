<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumRule implements Rule
{
    private string $enumClass;

    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    public function passes($attribute, $value): bool
    {
        return in_array($value, $this->enumClass::values(), true);
    }

    public function message(): string
    {
        return 'The :attribute field is not a valid value.';
    }
}
