<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Alerte;
use App\Security\UserPersonneInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Alerte>
 */
class AlerteVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Alerte;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserPersonneInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->handleEdit($subject, $user);
            case self::VIEW:
                return $this->handleView($subject, $user);
            case self::DELETE:
                return $this->handleDelete($subject, $user);
        }

        return false;
    }

    private function handleEdit(Alerte $subject, UserPersonneInterface $user): bool
    {
        return $subject->getPersonne() === $user;
    }

    private function handleView(Alerte $subject, UserPersonneInterface $user): bool
    {
        return $subject->getPersonne() === $user;
    }

    private function handleDelete(Alerte $subject, UserPersonneInterface $user): bool
    {
        return $subject->getPersonne() === $user;
    }
}
