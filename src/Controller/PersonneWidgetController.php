<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\PersonneWidget;
use App\Entity\Widget;
use App\Repository\PersonneWidgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/mes-widgets')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class PersonneWidgetController extends AbstractController
{
    #[Route('/', name: 'app_mes_widgets', methods: ['GET'])]
    public function index(): Response
    {
        /** @var Personne $personne */
        $personne = $this->getUser();

        return $this->render('personne/mes-widgets.html.twig', [
            'personne' => $personne,
        ]);
    }

    #[Route('/toggle', name: 'app_mes_widgets_toggle', methods: ['POST'])]
    public function toggle(Request $request, EntityManagerInterface $entityManager, PersonneWidgetRepository $personneWidgetRepository): Response
    {
        $widget_id = $request->request->get('id');
        /** @var Personne $personne */
        $personne = $this->getUser();

        /** @var Widget $widget */
        $widget = $entityManager->getRepository(Widget::class)->find($widget_id);
        $wp = $entityManager->getRepository(PersonneWidget::class)->findOneBy(
            ['personne' => $personne, 'widget' => $widget]
        );

        if ($wp) {
            $entityManager->remove($wp);
        } else {
            $wp = new PersonneWidget($personne, $widget);
            $entityManager->persist($wp);
        }
        $entityManager->flush();

        return new JsonResponse(['status' => $wp->getId() ? true : false]);
    }

    #[Route(path: '/setOrder', name: 'app_mes_widgets_order', methods: ['POST'])]
    public function sort(Request $request, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager): JsonResponse
    {
        $index = 0;
        /** @var Personne $personne */
        $personne = $this->getUser();
        $order = (string) $request->request->get('order');

        foreach (explode(',', $order) as $id) {
            $widget = $entityManager->getRepository(Widget::class)->find($id);
            if ($wp = $entityManager->getRepository(PersonneWidget::class)->findOneBy([
                'personne' => $personne,
                'widget' => $widget,
            ])) {
                $wp->setSortable($index);
                $index++;
            }
            $entityManager->flush();
        }

        return new JsonResponse(['order' => $order]);
    }
}
