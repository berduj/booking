<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

interface UserPersonneInterface extends UserInterface
{
    public function getId(): ?Uuid;
}
