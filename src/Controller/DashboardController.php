<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Alerte;
use App\Entity\Personne;
use App\Repository\AlerteRepository;
use App\Service\WidgetProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard_index')]
    #[IsGranted('ROLE_USER')]
    public function index(WidgetProvider $widgetProvider, AlerteRepository $alerteRepository): Response
    {
        /** @var Personne $user */
        $user = $this->getUser();

        return $this->render('dashboard/index.html.twig', [
            'widgets' => $widgetProvider->getPersonneWidgets($user),
            'alertes' => $alerteRepository->findForHomepageUser($user),
        ]);
    }

    #[Route('/dashboard/disable/{id}', name: 'app_dashboard_disable_alerte', methods: ['GET'])]
    #[IsGranted('EDIT', 'alerte')]
    public function disableAlerte(Alerte $alerte, EntityManagerInterface $entityManager): Response
    {
        $alerte->setEnabled(false);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard_index');
    }
}
