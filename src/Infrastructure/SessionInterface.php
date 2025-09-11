<?php

declare(strict_types=1);

namespace App\Infrastructure;

interface SessionInterface
{
    public function set(string $key, mixed $value): void;

    public function get(string $key, mixed $default): int|float|string|bool|null;
}
