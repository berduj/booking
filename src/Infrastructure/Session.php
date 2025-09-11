<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Symfony\Component\HttpFoundation\RequestStack;

class Session implements SessionInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function get(string $key, mixed $default = null): int|float|string|bool|null
    {
        return $this->requestStack->getSession()->get($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        $this->requestStack->getSession()->set($key, $value);
    }
}
