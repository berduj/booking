<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Personne;
use App\Entity\Structure;
use App\Security\UserPersonneInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Structure>
 */
class StructureVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const DELETE = 'DELETE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Structure;
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

    private function handleEdit(Structure $subject, UserPersonneInterface $user): bool
    {
        if ($subject->isAdminOnly()) {
            return $this->security->isGranted('ROLE_ADMIN');
        }

        if ($this->security->isGranted('ROLE_STRUCTURE_EDIT')) {
            return true;
        }


        return false;
    }

    private function handleView(Structure $subject, UserPersonneInterface $user): bool
    {
        return $this->security->isGranted('ROLE_STRUCTURE_VIEW');
    }

    private function handleDelete(Structure $subject, UserPersonneInterface $user): bool
    {
        return $this->security->isGranted('ROLE_STRUCTURE_DELETE');
    }
}
