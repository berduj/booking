<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SortableEntity\SortableEntityEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SortableEntityController extends AbstractController
{
    #[Route(path: '/sortableEntity/setOrder', name: 'sortable_entity', methods: ['POST'])]
    public function sort(Request $request, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $entity = (string) $request->request->get('entity');
        $order = (string) $request->request->get('order');

        $event = new SortableEntityEvent(explode(',', $order), $entity);
        $eventDispatcher->dispatch($event);

        return new JsonResponse(['order' => $order, 'entity' => $entity]);
    }
}
