<?php

declare(strict_types=1);

namespace App\Controller\Tools;

use App\Entity\Structure;
use App\Service\Geocoder\GeocodeEntityEvent;
use App\Service\Siret\SiretExistsException;
use App\Service\StructureFactory;
use App\Service\StructureSiretUpdater;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/structure')]
#[IsGranted('ROLE_STRUCTURE_VIEW')]
class ImportFromSiretController extends AbstractController
{
    #[Route('/add-siret', name: 'app_structure_add_siret', methods: ['POST'])]
    #[IsGranted('ROLE_STRUCTURE_CREATE')]
    public function addSiret(Request $request, EntityManagerInterface $entityManager, StructureFactory $structureFactory, EventDispatcherInterface $eventDispatcher, ValidatorInterface $validator): Response
    {
        $siret = (string) $request->request->get('siret');
        $siret = (string) preg_replace('/\s+/', '', $siret);
        $structure = $entityManager->getRepository(Structure::class)->findOneBy(['siret' => $siret]);

        try {
            if ($structure instanceof Structure) {
                throw new SiretExistsException('Il y a déjà une structure avec le Siret '.$siret.' : '.$structure->getRaisonSociale());
            }

            $structure = $structureFactory->createFromSiret($siret);

            $eventDispatcher->dispatch(new GeocodeEntityEvent($structure));
            $entityManager->persist($structure);
            $entityManager->flush();
            $this->addFlash('success', 'Structure créée');

            return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId()]);
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('app_structure_index');
    }

    #[Route('/update-from-siret/{id}', name: 'app_structure_update_from_siret', methods: ['GET'])]
    #[IsGranted('EDIT', 'structure')]
    public function updateForSiret(Structure $structure, StructureSiretUpdater $updater, EntityManagerInterface $entityManager): Response
    {
        try {
            $updater->update($structure);
            $entityManager->flush();
            $this->addFlash('success', 'Structure mise à jour');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Operation impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId(), 'tab' => 'struture'], Response::HTTP_SEE_OTHER);
    }
}
