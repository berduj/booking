<?php

declare(strict_types=1);

namespace App\Validator\Delete;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class DeleteStructure extends Constraint
{
    public string $message = 'The value "{{ value }}" is not valid.';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getRequiredOptions(): array
    {
        return ['groups'];
    }

    public function getDefaultOption(): string
    {
        return 'groups';
    }

    /**
     * @return array<string, mixed> an array of default options with a 'groups' key
     */
    public function getDefaultOptions(): array
    {
        return ['groups' => ['delete']];
    }
}
