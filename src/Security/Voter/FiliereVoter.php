<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Filiere;
use App\Security\UserPersonneInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Filiere>
 */
class FiliereVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Filiere;
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
        }

        return false;
    }

    private function handleEdit(Filiere $subject, UserPersonneInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PARAMETRAGE');
    }

    private function handleView(Filiere $subject, UserPersonneInterface $user): bool
    {
        return $this->security->isGranted('ROLE_PARAMETRAGE');
    }
}
