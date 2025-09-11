<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;

final class LoggdedInUser
{
    public function __construct(private readonly Security $security)
    {
    }

    public function getUser(): UserPersonneInterface
    {
        $user = $this->security->getUser();
        if ($user instanceof UserPersonneInterface) {
            return $user;
        }

        throw new \Exception('User is not logged in');
    }
}
