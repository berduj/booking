<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

use Symfony\Component\Uid\Uuid;

interface DocumentableInterface
{
    public function getId(): Uuid|int|null;
}
