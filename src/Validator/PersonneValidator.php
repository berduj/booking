<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PersonneValidator extends ConstraintValidator
{
    /**
     * @param \App\Entity\Personne $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value->getProfil() && !$value->getUsername()) {
            $this->context->buildViolation('La personne ne peut pas avoir de profilsi elle n\'a pas d\'identifiant')
                ->atPath('profil')
                ->addViolation();
        }
    }
}
