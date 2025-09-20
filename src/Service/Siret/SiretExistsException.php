<?php

declare(strict_types=1);

namespace App\Service\Siret;

class SiretExistsException extends SiretException
{
    public function __construct(?string $message = null, int $code = 0)
    {
        if ($message === null) {
            $message = 'Il y a déjà une structure avec ce SIRET';
        }
        parent::__construct($message, $code);
    }
}
