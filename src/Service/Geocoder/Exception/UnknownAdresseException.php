<?php

declare(strict_types=1);

namespace App\Service\Geocoder\Exception;

class UnknownAdresseException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->message = 'Adresse inconnue';
        parent::__construct($message, $code, $previous);
    }
}
