<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Personne;
use App\Entity\Widget;
use App\Widget\WidgetInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class WidgetProvider
{
    /** @var array<int,WidgetInterface> */
    private array $widgets = [];

    /** @param iterable<WidgetInterface> $widgets */
    public function __construct(#[AutowireIterator('app.widget')] iterable $widgets)
    {
        $array = [];
        foreach ($widgets as $widget) {
            $reflection = new \ReflectionClass($widget);

            if ($reflection->isInterface() or $reflection->isAbstract()) {
                continue;
            }
            if (!$reflection->hasMethod('getTemplate')) {
                continue;
            }

            $array[] = $widget;
        }

        $this->widgets = $array;
    }

    /**
     * @return array|WidgetInterface[]
     */
    public function getAllWidgets(): array
    {
        return $this->widgets;
    }

    /**
     * @return array|WidgetInterface[]
     */
    public function getPersonneWidgets(Personne $personne): array
    {
        $widgets = [];

        /** @var Widget $widget */
        foreach ($personne->getWidgets() as $widget) {
            foreach ($this->widgets as $widgetService) {
                if ($widget->getClass() === get_class($widgetService)) {
                    $widgets[] = $widgetService;

                    break;
                }
            }
        }

        return $widgets;
    }
}
