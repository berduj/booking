<?php

declare(strict_types=1);

namespace App\Validator\Delete;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DeletePersonneValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value->getStructures()->count()) {
            $this->context->buildViolation('Il y a des structures rattachées à la personne ('.$value->getStructures()->count().')')
                ->addViolation();
        }

        if (count($value->getAuteurContacts()) > 0) {
            $this->context->buildViolation('La personne est l\'auteur de contacts ('.$value->getAuteurContacts()->count().')')
                ->addViolation();
        }

        if ($value->getContactInterlocuteurs()->count() > 0) {
            $this->context->buildViolation('La personne est l\'interlocuteur de contacts ('.$value->getContactInterlocuteurs()->count().')')
                ->addViolation();
        }

        if ($value->getActionPilotages()->count() > 0) {
            $this->context->buildViolation('La personne pilote des actions ('.$value->getActionPilotages()->count().')')
                ->addViolation();
        }

        if ($value->getActionParticipants()->count() > 0) {
            $this->context->buildViolation('La personne participe à des actions ('.$value->getActionParticipants()->count().')')
                ->addViolation();
        }

        if ($value->getReferentStructures()->count()) {
            $this->context->buildViolation('La personne est référente de structures ('.$value->getReferentStructures()->count().')')
                ->addViolation();
        }
    }
}
