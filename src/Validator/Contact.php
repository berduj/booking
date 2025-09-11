<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Contact extends Constraint
{
    public string $message = 'The value "{{ value }}" is not valid.';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
