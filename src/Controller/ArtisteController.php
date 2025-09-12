<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Artiste;
use App\Entity\Personne;
use App\Form\ArtisteType;
use App\Repository\ArtisteRepository;
use App\Repository\ContactRepository;
use App\Repository\PersonneRepository;
use App\Service\Geocoder\GeocodeEntityEvent;
use App\Service\Persister\EnabledDisabledAllPersister;
use App\Service\Persister\InitialPersister;
use App\ValueObject\EnabledDisabledAll;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/artiste')]
class ArtisteController extends AbstractController
{
    #[Route('/', name: 'app_artiste_index', methods: ['GET'], defaults: ['page' => null, 'status' => null])]
    #[Route('/page/{page}', name: 'app_artiste_index_page', methods: ['GET'], defaults: ['status' => null])]
    #[Route('/status/{status}', name: 'app_artiste_index_status', methods: ['GET'], defaults: ['page' => null])]
    #[IsGranted('ROLE_ARTISTE_VIEW')]
    public function index(?string $page, ?string $status, ArtisteRepository $artisteRepository, InitialPersister $initialPersister, EnabledDisabledAllPersister $enabledDisabledAllPersister): Response
    {
        if ($page) {
            $initialPersister->set($page, 'artiste');
        }

        if ($status) {
            $enabledDisabledAllPersister->set(new EnabledDisabledAll($status), 'artiste');
        }

        $page = $initialPersister->get('artiste', 'A');
        $status = $enabledDisabledAllPersister->get('artiste', EnabledDisabledAll::ENABLED);

        return $this->render('artiste/index.html.twig', [
            'artistes' => $artisteRepository->findByLetterStatus($page, $status),
            'active_page' => $page,
            'status' => $status,
        ]);
    }

    #[Route('/new', name: 'app_artiste_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ARTISTE_CREATE')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $artiste = new Artiste();
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($artiste);
            $entityManager->flush();

            return $this->redirectToRoute('app_artiste_show', ['id' => $artiste->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('artiste/new.html.twig', [
                'artiste' => $artiste,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_artiste_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'artiste')]
    public function show(Artiste $artiste, ContactRepository $contactRepository): Response
    {
        return $this->render('artiste/show.html.twig', [
            'artiste' => $artiste,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_artiste_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'artiste')]
    public function edit(Request $request, Artiste $artiste, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_artiste_show', ['id' => $artiste->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artiste/edit.html.twig', [
            'artiste' => $artiste,
            'form' => $form,
        ]);
    }

    #[Route('/geocode/{id}', name: 'app_artiste_geocode', methods: ['GET'])]
    #[IsGranted('EDIT', 'artiste')]
    public function geocode(Artiste $artiste, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher): Response
    {
        $event = new GeocodeEntityEvent($artiste);
        $eventDispatcher->dispatch($event);
        $entityManager->flush();

        return $this->redirectToRoute('app_artiste_show', ['id' => $artiste->getId()]);
    }

    #[Route('/delete/{id}', name: 'app_artiste_delete', methods: ['POST'])]
    #[IsGranted('DELETE', 'artiste')]
    public function delete(
        Request $request,
        Artiste $artiste,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$artiste->getId(), (string) $request->getPayload()->get('_token'))) {
            try {
                $errors = $validator->validate($artiste, null, ['delete']);
                if (count($errors)) {
                    $message = "<b>Suppression impossible : </b>\n";
                    foreach ($errors as $error) {
                        $message .= ' - '.$error->getMessage()."\n";
                    }
                    throw new \Exception(nl2br($message));
                }

                $entityManager->remove($artiste);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('app_artiste_show', ['id' => $artiste->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->redirectToRoute('app_artiste_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/autocomplete', name: 'app_artiste_personne_autocomplete', methods: ['POST'])]
    public function personneAutocomplete(Request $request, PersonneRepository $personneRepository): JsonResponse
    {
        $query = (string) $request->request->get('query');
        $ret = $personneRepository->findAutocomplete($query, 15);

        return new JsonResponse($ret);
    }

    #[Route('/add-personne', name: 'app_artiste_add_personne', methods: ['POST'])]
    public function addPersonne(Request $request, EntityManagerInterface $entityManager): Response
    {
        $personne = $entityManager->getRepository(Personne::class)->find($request->request->get('personne'));
        $artiste = $entityManager->getRepository(Artiste::class)->find($request->request->get('artiste'));
        if (!$personne || !$artiste) {
            throw new \InvalidArgumentException('personne ou artiste inconnue');
        }

        try {
            if ($personne->getArtistes()->contains($artiste)) {
                throw new \InvalidArgumentException($personne.' est déjà rattaché(e) à '.$artiste);
            }
            $personne->addArtiste($artiste);
            $entityManager->flush();
            $this->addFlash('success', $personne.' a été rattaché(e) à '.$artiste);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Ajout impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_artiste_show', ['id' => $artiste->getId(), 'tab' => 'personnes'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete-personne/{personne}/{artiste}', name: 'app_artiste_remove_personne', methods: ['GET'])]
    #[IsGranted('EDIT', 'artiste')]
    public function removePersonne(Personne $personne, Artiste $artiste, EntityManagerInterface $entityManager): Response
    {
        try {
            $personne->removeArtiste($artiste);
            $this->addFlash('success', $personne.' a été supprimé(e) de '.$artiste);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Suppression impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_artiste_show', ['id' => $artiste->getId(), 'tab' => 'personnes'], Response::HTTP_SEE_OTHER);
    }
}
