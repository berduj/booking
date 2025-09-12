<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Artiste;
use App\Entity\Personne;
use App\Form\ArtisteType;
use App\Repository\ArtisteRepository;
use App\Repository\ContactRepository;
use App\Repository\PersonneRepository;
use App\Repository\StructureRepository;
use App\Service\Persister\EnabledDisabledAllPersister;
use App\Service\Persister\InitialPersister;
use App\ValueObject\EnabledDisabledAll;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/carte')]
class CarteController extends AbstractController
{
    #[Route('/artiste', name: 'app_carte_artiste', methods: ['GET'])]
    #[IsGranted('ROLE_ARTISTE_VIEW')]
    public function artiste( ArtisteRepository $artisteRepository): Response
    {

        return $this->render('carte/artiste.html.twig', [
            'artistes' => $artisteRepository->findBy(['enabled' => true]),
        ]);
    }
    #[Route('/structure', name: 'app_carte_structure', methods: ['GET'])]
    #[IsGranted('ROLE_STRUCTURE_VIEW')]
    public function structure( StructureRepository $structureRepository): Response
    {

        return $this->render('carte/structure.html.twig', [
            'structures' => $structureRepository->findBy(['enabled' => true]),
        ]);
    }
}
