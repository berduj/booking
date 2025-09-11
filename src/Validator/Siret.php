<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class Siret extends Constraint
{
    public string $message = 'Le numéro de siret "{{ string }}" est invalide.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
