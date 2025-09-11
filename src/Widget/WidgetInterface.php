<?php

declare(strict_types=1);

namespace App\Widget;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.widget')]
interface WidgetInterface
{
    public function getWidth(): int;

    public function getTitle(): string;

    public function getTemplate(): string;
}
