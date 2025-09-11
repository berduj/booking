<?php

declare(strict_types=1);

namespace App\Service\Persister;

use App\Infrastructure\SessionInterface;
use App\ValueObject\EnabledDisabledAll;

class EnabledDisabledAllPersister
{
    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function set(EnabledDisabledAll $status, string $prefix): void
    {
        $this->session->set($prefix.'EnabledDisabledAll', $status->getValue());
    }

    public function get(string $prefix, ?string $default = EnabledDisabledAll::ENABLED): EnabledDisabledAll
    {
        return new EnabledDisabledAll((string) $this->session->get($prefix.'EnabledDisabledAll', $default));
    }
}
