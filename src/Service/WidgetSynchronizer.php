<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Widget;
use App\Widget\WidgetInterface;
use Doctrine\ORM\EntityManagerInterface;

class WidgetSynchronizer
{
    public function __construct(private EntityManagerInterface $entityManager, private readonly WidgetProvider $widgetProvider)
    {
    }

    public function synchronize(): void
    {
        $widgetRepository = $this->entityManager->getRepository(Widget::class);
        foreach ($this->widgetProvider->getAllWidgets() as $widgetService) {
            /** @var WidgetInterface $widgetService */
            $widget = $widgetRepository->findOneBy(['class' => get_class($widgetService)]);
            if (!$widget) {
                $widget = new Widget();
                $widget->setClass(get_class($widgetService));
                $widget->setLibelle($widgetService->getTitle());
                $this->entityManager->persist($widget);
            }
        }
        $this->entityManager->flush();
    }
}
