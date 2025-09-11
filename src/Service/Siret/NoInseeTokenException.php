<?php

declare(strict_types=1);

namespace App\Service\Siret;

class NoInseeTokenException extends SiretException
{
    public function __construct(?string $message = null, int $code = 0)
    {
        if ($message === null) {
            $message = 'Unable to get INSEE Token';
        }
        parent::__construct($message, $code);
    }
}
