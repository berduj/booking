<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Artiste;
use App\Security\UserPersonneInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Artiste>
 */
class ArtisteVoter extends Voter
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
            && $subject instanceof Artiste;
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

    private function handleEdit(Artiste $subject, UserPersonneInterface $user): bool
    {
        if ($this->security->isGranted('ROLE_ARTISTE_EDIT')) {
            return true;
        }

        return false;
    }

    private function handleView(Artiste $subject, UserPersonneInterface $user): bool
    {
        if ($user === $subject) {
            return true;
        }

        return $this->security->isGranted('ROLE_ARTISTE_VIEW');
    }

    private function handleDelete(Artiste $subject, UserPersonneInterface $user): bool
    {
        return $this->security->isGranted('ROLE_ARTISTE_DELETE');
    }
}
