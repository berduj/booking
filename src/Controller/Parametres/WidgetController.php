<?php

declare(strict_types=1);

namespace App\Controller\Parametres;

use App\Repository\ProfilRepository;
use App\Repository\WidgetRepository;
use App\Service\WidgetSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parametres/widgets')]
#[IsGranted('ROLE_PARAMETRAGE')]
class WidgetController extends AbstractController
{
    #[Route('/', name: 'app_widget_index', methods: ['GET'])]
    public function index(
        WidgetSynchronizer $synchronizer,
        WidgetRepository $widgetRepository,
        ProfilRepository $profilRepository,
    ): Response {
        $synchronizer->synchronize();
        $widgets = $widgetRepository->findAll();
        $profils = $profilRepository->findAll();

        return $this->render('parametres/widget/index.html.twig', [
            'widgets' => $widgets,
            'profils' => $profils,
        ]);
    }

    #[Route('/update', name: 'app_widget_toggle', methods: ['POST'])]
    public function toggle(
        Request $request,
        WidgetRepository $widgetRepository,
        ProfilRepository $profilRepository,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $id = (string) $request->request->get('id');
        [$profil_id, $widget_id] = explode('-', $id);
        $profil = $profilRepository->find($profil_id);
        $widget = $widgetRepository->find($widget_id);

        if ($widget === null || $profil === null) {
            throw new \InvalidArgumentException(sprintf('Widget %s not found or profil %s not found', $widget_id, $profil_id));
        }

        if ($widget->getProfils()->contains($profil)) {
            $widget->removeProfil($profil);
        } else {
            $widget->addProfil($profil);
        }
        $entityManager->flush();

        return new JsonResponse(
            [
                'status' => $widget->getProfils()->contains($profil),
            ]
        );
    }
}
