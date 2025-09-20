<?php

declare(strict_types=1);

namespace App\Service\Siret;

class InvalidSiretFormatException extends SiretException
{
    public function __construct(?string $message = null, int $code = 0)
    {
        if ($message === null) {
            $message = 'Le format du SIRET est invalide : '.InseeApiSiret::SIRET_LENGTH.' caractères numériques attendus';
        }
        parent::__construct($message, $code);
    }
}
