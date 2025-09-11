<?php

declare(strict_types=1);

namespace App\Service\Geocoder\Exception;

class GeocodageErrorException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->message = 'Erreur de geocodage';
        parent::__construct($message, $code, $previous);
    }
}
