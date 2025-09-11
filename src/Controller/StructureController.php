<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\Structure;
use App\Form\ImportSiretExcelType;
use App\Form\StructureType;
use App\Repository\ActionParticipantRepository;
use App\Repository\ActionPilotageRepository;
use App\Repository\ContactRepository;
use App\Repository\PersonneRepository;
use App\Repository\StructureRepository;
use App\Service\Geocoder\GeocodeEntityEvent;
use App\Service\Import\ImportSiret;
use App\Service\Persister\EnabledDisabledAllPersister;
use App\Service\Persister\InitialPersister;
use App\ValueObject\EnabledDisabledAll;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/structure')]
#[IsGranted('ROLE_STRUCTURE_VIEW')]
class StructureController extends AbstractController
{
    #[Route('/', name: 'app_structure_index', methods: ['GET'], defaults: ['page' => null, 'status' => null])]
    #[Route('/page/{page}', name: 'app_structure_index_page', methods: ['GET'], defaults: ['status' => null])]
    #[Route('/status/{status}', name: 'app_structure_index_status', methods: ['GET'], defaults: ['page' => null])]
    public function index(?string $page, ?string $status, StructureRepository $structureRepository, InitialPersister $initialPersister, EnabledDisabledAllPersister $enabledDisabledAllPersister): Response
    {
        if ($page) {
            $initialPersister->set($page, 'structure');
        }
        if ($status) {
            $enabledDisabledAllPersister->set(new EnabledDisabledAll($status), 'structure');
        }

        $page = $initialPersister->get('structure', 'A');
        $status = $enabledDisabledAllPersister->get('structure', EnabledDisabledAll::ENABLED);

        return $this->render('structure/index.html.twig', [
            'structures' => $structureRepository->findByLetterStatus($page, $status),
            'active_page' => $page,
            'status' => $status,
        ]);
    }

    #[Route('/new', name: 'app_structure_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_STRUCTURE_CREATE')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $structure = new Structure();
        $form = $this->createForm(StructureType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($structure);
            $entityManager->flush();

            return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('structure/new.html.twig', [
                'structure' => $structure,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_structure_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'structure')]
    public function show(
        Structure $structure,
        ContactRepository $contactRepository,
    ): Response {
        return $this->render('structure/show.html.twig', [
            'structure' => $structure,
            'contacts' => $contactRepository->findByStructure($structure),
        ]);
    }

    #[Route('/geocode/{id}', name: 'app_structure_geocode', methods: ['GET'])]
    #[IsGranted('EDIT', 'structure')]
    public function geocode(Structure $structure, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher): Response
    {
        $event = new GeocodeEntityEvent($structure);
        $eventDispatcher->dispatch($event);
        $entityManager->flush();

        return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId()]);
    }

    #[Route('/edit/{id}', name: 'app_structure_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'structure')]
    public function edit(Request $request, Structure $structure, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StructureType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('structure/edit.html.twig', [
            'structure' => $structure,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_structure_delete', methods: ['POST'])]
    #[IsGranted('DELETE', 'structure')]
    public function delete(
        Request $request,
        Structure $structure,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$structure->getId(), (string) $request->getPayload()->get('_token'))) {
            try {
                $errors = $validator->validate($structure, null, ['delete']);
                if (count($errors)) {
                    $message = "<b>Suppression impossible : </b>\n";
                    foreach ($errors as $error) {
                        $message .= ' - '.$error->getMessage()."\n";
                    }
                    throw new \Exception(nl2br($message));
                }

                $entityManager->remove($structure);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->redirectToRoute('app_structure_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/autocomplete-personne', name: 'app_structure_personne_autocomplete', methods: ['POST'])]
    public function personneAutocomplete(Request $request, PersonneRepository $personneRepository): JsonResponse
    {
        $query = (string) $request->request->get('query');
        $ret = $personneRepository->findAutocomplete($query, 10);

        return new JsonResponse($ret);
    }

    #[Route('/add-personne', name: 'app_structure_add_personne', methods: ['POST'])]
    public function addPersonne(Request $request, EntityManagerInterface $entityManager): Response
    {
        $structure = $entityManager->getRepository(Structure::class)->find($request->request->get('structure'));
        $personne = $entityManager->getRepository(Personne::class)->find($request->request->get('personne'));

        if (!$structure || !$personne) {
            throw new \InvalidArgumentException('structure ou personne inconnue');
        }

        try {
            if ($structure->getPersonnes()->contains($personne)) {
                throw new \InvalidArgumentException($personne.' est déjà rattaché(e) à '.$structure);
            }
            $structure->addPersonne($personne);
            $entityManager->flush();
            $this->addFlash('success', $personne.' a été ajouté(e) à '.$structure);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Ajout impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId(), 'tab' => 'personnes'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete-personne/{structure}/{personne}', name: 'app_structure_remove_personne', methods: ['GET'])]
    #[IsGranted('EDIT', 'structure')]
    public function removePersonne(Structure $structure, Personne $personne, EntityManagerInterface $entityManager): Response
    {
        try {
            $structure->removePersonne($personne);
            $this->addFlash('success', $personne.' a été supprimé(e) de '.$structure);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Suppression impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId(), 'tab' => 'personnes'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/autocomplete-referent', name: 'app_structure_referent_autocomplete', methods: ['POST'])]
    public function referentAutocomplete(Request $request, PersonneRepository $personneRepository): JsonResponse
    {
        $query = (string) $request->request->get('query');
        $ret = $personneRepository->findAutocompleteReferent($query);

        return new JsonResponse($ret);
    }

    #[Route('/add-referent', name: 'app_structure_add_referent', methods: ['POST'])]
    public function addReferent(Request $request, EntityManagerInterface $entityManager): Response
    {
        $structure = $entityManager->getRepository(Structure::class)->find($request->request->get('structure'));
        $referent = $entityManager->getRepository(Personne::class)->find($request->request->get('referent'));

        if (!$structure || !$referent) {
            throw new \InvalidArgumentException('structure ou personne inconnue');
        }

        try {
            if ($structure->getReferents()->contains($referent)) {
                throw new \InvalidArgumentException($referent.' est déjà rattaché(e) à '.$structure);
            }
            $structure->addReferent($referent);
            $entityManager->flush();
            $this->addFlash('success', $referent.' a été ajouté(e) à '.$structure);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Ajout impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId(), 'tab' => 'referents'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete-referent/{structure}/{referent}', name: 'app_structure_remove_referent', methods: ['GET'])]
    #[IsGranted('DELETE', 'structure')]
    public function removeReferent(Structure $structure, Personne $referent, EntityManagerInterface $entityManager): Response
    {
        try {
            $structure->removeReferent($referent);
            $this->addFlash('success', $referent.' a été supprimé(e) de '.$structure);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Suppression impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId(), 'tab' => 'referents'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/referent-principal/{structure}/{referent}', name: 'app_structure_referent_principal', methods: ['GET'])]
    #[IsGranted('EDIT', 'structure')]
    public function principalReferent(Structure $structure, Personne $referent, EntityManagerInterface $entityManager): Response
    {
        try {
            if ($structure->getReferentPrincipal() === $referent) {
                $structure->setReferentPrincipal(null);
            } else {
                $structure->setReferentPrincipal($referent);
            }
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Operation impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId(), 'tab' => 'referents'], Response::HTTP_SEE_OTHER);
    }
}
