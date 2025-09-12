<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArtisteRepository;
use App\Repository\StructureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/carte')]
class CarteController extends AbstractController
{
    #[Route('/artiste', name: 'app_carte_artiste', methods: ['GET'])]
    #[IsGranted('ROLE_ARTISTE_VIEW')]
    public function artiste(ArtisteRepository $artisteRepository): Response
    {
        return $this->render('carte/artiste.html.twig', [
            'artistes' => $artisteRepository->findBy(['enabled' => true]),
        ]);
    }

    #[Route('/structure', name: 'app_carte_structure', methods: ['GET'])]
    #[IsGranted('ROLE_STRUCTURE_VIEW')]
    public function structure(StructureRepository $structureRepository): Response
    {
        return $this->render('carte/structure.html.twig', [
            'structures' => $structureRepository->findBy(['enabled' => true]),
        ]);
    }
}
