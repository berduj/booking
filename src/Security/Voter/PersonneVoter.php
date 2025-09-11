<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Personne;
use App\Security\UserPersonneInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Personne>
 */
class PersonneVoter extends Voter
{
    public const DELETE = 'DELETE';
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Personne;
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

    private function handleEdit(Personne $subject, UserPersonneInterface $user): bool
    {
        if ($subject->isVip() and !$this->security->isGranted('ROLE_VIP_EDIT')) {
            return false;
        }

        if ($this->security->isGranted('ROLE_PERSONNE_EDIT')) {
            return true;
        }

        return false;
    }

    private function handleView(Personne $subject, UserPersonneInterface $user): bool
    {
        if ($user === $subject) {
            return true;
        }

        if ($subject->isVip() and !$this->security->isGranted('ROLE_VIP_EDIT')) {
            return false;
        }

        return $this->security->isGranted('ROLE_PERSONNE_VIEW');
    }

    private function handleDelete(Personne $subject, UserPersonneInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PERSONNE_DELETE');
    }
}
