<?php

declare(strict_types=1);

namespace App\Controller\Parametres;

use App\Entity\Filiere;
use App\Form\FiliereType;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parametres/filiere')]
#[IsGranted('ROLE_PARAMETRAGE')]
class FiliereController extends AbstractController
{
    #[Route('/', name: 'app_filiere_index', methods: ['GET'])]
    public function index(FiliereRepository $filiereRepository): Response
    {
        return $this->render('parametres/filiere/index.html.twig', [
            'filieres' => $filiereRepository->findBy([], ['sortable' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_filiere_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $filiere = new Filiere();
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($filiere);
            $entityManager->flush();

            return $this->redirectToRoute('app_filiere_show', ['id' => $filiere->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('parametres/filiere/new.html.twig', [
                'filiere' => $filiere,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_filiere_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'filiere')]
    public function show(Filiere $filiere): Response
    {
        return $this->render('parametres/filiere/show.html.twig', [
            'filiere' => $filiere,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_filiere_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'filiere')]
    public function edit(Request $request, Filiere $filiere, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($filiere);
            $entityManager->flush();

            return $this->redirectToRoute('app_filiere_show', ['id' => $filiere->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres/filiere/edit.html.twig', [
            'filiere' => $filiere,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_filiere_delete', methods: ['POST'])]
    #[IsGranted('EDIT', 'filiere')]
    public function delete(Request $request, Filiere $filiere, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$filiere->getId(), (string) $request->getPayload()->get('_token'))) {
            $entityManager->remove($filiere);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_filiere_index', [], Response::HTTP_SEE_OTHER);
    }
}
