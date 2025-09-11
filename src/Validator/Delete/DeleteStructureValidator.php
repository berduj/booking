<?php

declare(strict_types=1);

namespace App\Validator\Delete;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeleteStructureValidator extends ConstraintValidator
{
    /**
     * @param \App\Entity\Structure $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value->getPersonnes()->count()) {
            $this->context->buildViolation('Il y a des personnes rattachées à la structure')
                ->addViolation();
        }

        if ($value->getContactInterlocuteurs()->count() > 0) {
            $this->context->buildViolation('La structure a des contacts')
                ->addViolation();
        }
    }
}
