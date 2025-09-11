<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContactValidator extends ConstraintValidator
{
    /**
     * @param \App\Entity\Contact $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $now = new \DateTime();
        $now->modify('+1 day');
        $now->setTime(0, 0, 0, 0);

        if ($value->getDate() > $now) {
            $this->context->buildViolation('La date ne peut pas être dans le futur')
                ->atPath('date')
                ->addViolation();
        }

        if ($value->getDate() < new \DateTime('2020-01-01')) {
            $this->context->buildViolation('La date est invalide : pas de date avant 2020')
                ->atPath('date')
                ->addViolation();
        }
    }
}
