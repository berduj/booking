<?php

declare(strict_types=1);

namespace App\ValueObject;

class EnabledDisabledAll
{
    private string $value;

    public const ENABLED = 'enabled';
    public const DISABLED = 'disabled';
    public const ALL = 'all';

    /**
     * @var array|string[]
     */
    private array $availableValues = [self::ENABLED, self::DISABLED, self::ALL];

    public function __construct(string $value)
    {
        if (!in_array($value, $this->availableValues)) {
            throw new \InvalidArgumentException("la valeur {$value} n'est pas valide ");
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getSqlEnabledFilter(): ?bool
    {
        if ($this->value === self::ENABLED) {
            return true;
        }
        if ($this->value === self::DISABLED) {
            return false;
        }

        return null;
    }
}
