<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Export;
use App\Entity\Personne;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Export>
 */
final class ExportVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const EDIT_CODE = 'EDIT_CODE';
    public const VIEW = 'VIEW';
    public const EXECUTE = 'EXECUTE';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::EXECUTE, self::EDIT_CODE])
            && $subject instanceof Export;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof Personne) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT_CODE:
                return $this->handleEditCode($subject, $user);
            case self::EDIT:
                return $this->handleEdit($subject, $user);
            case self::VIEW:
                return $this->handleView($subject, $user);
            case self::EXECUTE:
                return $this->handleExecute($subject, $user);
        }

        return false;
    }

    private function handleEditCode(Export $subject, Personne $user): bool
    {
        return $this->security->isGranted('ROLE_SUPER_ADMIN');
    }

    private function handleView(Export $subject, Personne $user): bool
    {
        return $this->security->isGranted('ROLE_PARAMETRAGE');
    }

    private function handleEdit(Export $subject, Personne $user): bool
    {
        return $this->security->isGranted('ROLE_PARAMETRAGE');
    }

    private function handleExecute(Export $subject, Personne $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $subject->getProfils()->contains($user->getProfil());
    }
}
