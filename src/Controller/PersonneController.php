<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\Structure;
use App\Form\PersonneType;
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

#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'app_personne_index', methods: ['GET'], defaults: ['page' => null, 'status' => null])]
    #[Route('/page/{page}', name: 'app_personne_index_page', methods: ['GET'], defaults: ['status' => null])]
    #[Route('/status/{status}', name: 'app_personne_index_status', methods: ['GET'], defaults: ['page' => null])]
    #[IsGranted('ROLE_PERSONNE_VIEW')]
    public function index(?string $page, ?string $status, PersonneRepository $personneRepository, InitialPersister $initialPersister, EnabledDisabledAllPersister $enabledDisabledAllPersister): Response
    {
        if ($page) {
            $initialPersister->set($page, 'personne');
        }

        if ($status) {
            $enabledDisabledAllPersister->set(new EnabledDisabledAll($status), 'personne');
        }

        $page = $initialPersister->get('personne', 'A');
        $status = $enabledDisabledAllPersister->get('personne', EnabledDisabledAll::ENABLED);

        return $this->render('personne/index.html.twig', [
            'personnes' => $personneRepository->findByLetterStatus($page, $status),
            'active_page' => $page,
            'status' => $status,
        ]);
    }

    #[Route('/new', name: 'app_personne_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PERSONNE_CREATE')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($personne);
            $entityManager->flush();

            return $this->redirectToRoute('app_personne_show', ['id' => $personne->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('personne/new.html.twig', [
                'personne' => $personne,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_personne_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'personne')]
    public function show(Personne $personne, ContactRepository $contactRepository): Response
    {
        return $this->render('personne/show.html.twig', [
            'personne' => $personne,
            'contacts' => $contactRepository->findByPersonne($personne),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_personne_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'personne')]
    public function edit(Request $request, Personne $personne, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_personne_show', ['id' => $personne->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('personne/edit.html.twig', [
            'personne' => $personne,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_personne_delete', methods: ['POST'])]
    #[IsGranted('DELETE', 'personne')]
    public function delete(
        Request $request,
        Personne $personne,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$personne->getId(), (string) $request->getPayload()->get('_token'))) {
            try {
                $errors = $validator->validate($personne, null, ['delete']);
                if (count($errors)) {
                    $message = "<b>Suppression impossible : </b>\n";
                    foreach ($errors as $error) {
                        $message .= ' - '.$error->getMessage()."\n";
                    }
                    throw new \Exception(nl2br($message));
                }

                $entityManager->remove($personne);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('app_personne_show', ['id' => $personne->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->redirectToRoute('app_personne_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/autocomplete', name: 'app_personne_structure_autocomplete', methods: ['POST'])]
    public function structureAutocomplete(Request $request, StructureRepository $structureRepository): JsonResponse
    {
        $query = (string) $request->request->get('query');
        $ret = $structureRepository->findAutocomplete($query, 15);

        return new JsonResponse($ret);
    }

    #[Route('/add-structure', name: 'app_personne_add_structure', methods: ['POST'])]
    public function addStructure(Request $request, EntityManagerInterface $entityManager): Response
    {
        $structure = $entityManager->getRepository(Structure::class)->find($request->request->get('structure'));
        $personne = $entityManager->getRepository(Personne::class)->find($request->request->get('personne'));
        if (!$structure || !$personne) {
            throw new \InvalidArgumentException('structure ou personne inconnue');
        }

        try {
            if ($structure->getPersonnes()->contains($personne)) {
                throw new \InvalidArgumentException($structure.' est déjà rattaché(e) à '.$personne);
            }
            $structure->addPersonne($personne);
            $entityManager->flush();
            $this->addFlash('success', $structure.' a été rattaché(e) à '.$personne);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Ajout impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_personne_show', ['id' => $personne->getId(), 'tab' => 'structures'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete-structure/{structure}/{personne}', name: 'app_personne_remove_structure', methods: ['GET'])]
    #[IsGranted('EDIT', 'personne')]
    public function removeStructure(Structure $structure, Personne $personne, EntityManagerInterface $entityManager): Response
    {
        try {
            $structure->removePersonne($personne);
            $this->addFlash('success', $structure.' a été supprimé(e) de '.$personne);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Suppression impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_personne_show', ['id' => $personne->getId(), 'tab' => 'structures'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/add-referent', name: 'app_personne_add_referent', methods: ['POST'])]
    public function addReferent(Request $request, EntityManagerInterface $entityManager): Response
    {
        $structure = $entityManager->getRepository(Structure::class)->find($request->request->get('structure'));
        $personne = $entityManager->getRepository(Personne::class)->find($request->request->get('personne'));
        if (!$structure || !$personne) {
            throw new \InvalidArgumentException('structure ou personne inconnue');
        }

        try {
            if ($structure->getReferents()->contains($personne)) {
                throw new \InvalidArgumentException($structure.' est déjà rattaché(e) à '.$personne);
            }
            $structure->addReferent($personne);
            $entityManager->flush();
            $this->addFlash('success', $structure.' a été rattaché(e) à '.$personne);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Ajout impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_personne_show', ['id' => $personne->getId(), 'tab' => 'referents'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete-referent/{structure}/{referent}', name: 'app_personne_remove_referent', methods: ['GET'])]
    #[IsGranted('EDIT', 'referent')]
    public function removeReferent(Structure $structure, Personne $referent, EntityManagerInterface $entityManager): Response
    {
        try {
            $structure->removeReferent($referent);
            $this->addFlash('success', $structure.' a été supprimé(e) de '.$referent);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Suppression impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_personne_show', ['id' => $referent->getId(), 'tab' => 'referents'], Response::HTTP_SEE_OTHER);
    }
}
