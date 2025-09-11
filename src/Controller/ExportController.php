<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Export;
use App\Entity\Personne;
use App\Repository\ExportRepository;
use App\Security\UserPersonneInterface;
use App\Service\Export\Excel\ExportExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/export')]
#[IsGranted('ROLE_USER')]
class ExportController extends AbstractController
{
    #[Route('/', name: 'app_export_public', methods: ['GET'])]
    public function index(ExportRepository $exportRepository): Response
    {
        /** @var Personne $personne */
        $personne = $this->getUser();

        return $this->render('export/public.html.twig', [
            'exports' => $exportRepository->findPublic($personne),
        ]);
    }

    #[Route(path: '/execute/{id}', name: 'app_export_execute', methods: ['GET'])]
    public function execute(Export $export, ExportExporter $exporter): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserPersonneInterface) {
            throw $this->createAccessDeniedException();
        }

        $exporter->setExport($export, $user);
        $exporter->execute();

        return $exporter->getResponse();
    }
}
