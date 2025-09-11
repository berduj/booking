<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class SiretValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var Siret $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $value = str_replace(' ', '', $value);

        if (!preg_match('/^\d{14}$/', $value)) {
            // Le numéro SIRET doit être une chaîne de 14 chiffres
            $this->context->buildViolation('Le numéro de siret "{{ string }}" est invalide.')
                ->setParameter('{{ string }}', $value)
                ->addViolation();

            return;
        }

        // Vérification de la clé de Luhn
        $sum = 0;
        for ($i = 0; $i < 14; $i++) {
            $digit = intval($value[$i]);
            if ($i % 2 === 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        if ($sum % 10 !== 0) {
            $this->context->buildViolation('Le numéro de siret "{{ string }}" est invalide.')
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
