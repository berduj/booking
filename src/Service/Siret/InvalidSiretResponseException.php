<?php

declare(strict_types=1);

namespace App\Service\Siret;

class InvalidSiretResponseException extends SiretException
{
    public function __construct(?string $message = null, int $code = 0)
    {
        if ($message === null) {
            $message = 'Le web service SIRET n\'a pas renvoyé de réponse pour cette valeur';
        }
        parent::__construct($message, $code);
    }
}
