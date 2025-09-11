<?php

declare(strict_types=1);

namespace App\Service\Persister;

use App\Infrastructure\SessionInterface;

class InitialPersister
{
    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function set(string $initial, string $prefix): void
    {
        $this->session->set($prefix.'Initial', $initial);
    }

    public function get(string $prefix, string $default = 'A'): string
    {
        return (string) $this->session->get($prefix.'Initial', $default);
    }
}
