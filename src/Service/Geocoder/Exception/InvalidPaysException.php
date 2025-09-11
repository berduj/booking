<?php

declare(strict_types=1);

namespace App\Service\Geocoder\Exception;

class InvalidPaysException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->message = 'Pays invalide (seules les adresses en France sont géocodées)';
        parent::__construct($message, $code, $previous);
    }
}
