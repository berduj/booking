<?php

declare(strict_types=1);

namespace App\Service\Persister;

use App\Infrastructure\SessionInterface;

class PagePersister
{
    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function set(int $page, string $prefix): void
    {
        $this->session->set($prefix.'Page', $page);
    }

    public function get(string $prefix, mixed $default = 1): ?int
    {
        $ret = $this->session->get($prefix.'Page', $default);
        if ($ret === null) {
            return null;
        }

        return (int) $ret;
    }
}
