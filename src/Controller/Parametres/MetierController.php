<?php

declare(strict_types=1);

namespace App\Controller\Parametres;

use App\Entity\Metier;
use App\Form\MetierType;
use App\Repository\MetierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parametres/metier')]
#[IsGranted('ROLE_PARAMETRAGE')]
class MetierController extends AbstractController
{
    #[Route('/', name: 'app_metier_index', methods: ['GET'])]
    public function index(MetierRepository $metierRepository): Response
    {
        return $this->render('parametres/metier/index.html.twig', [
            'metiers' => $metierRepository->findBy([], ['sortable' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_metier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $metier = new Metier();
        $form = $this->createForm(MetierType::class, $metier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($metier);
            $entityManager->flush();

            return $this->redirectToRoute('app_metier_show', ['id' => $metier->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('parametres/metier/new.html.twig', [
                'metier' => $metier,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_metier_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'metier')]
    public function show(Metier $metier): Response
    {
        return $this->render('parametres/metier/show.html.twig', [
            'metier' => $metier,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_metier_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'metier')]
    public function edit(Request $request, Metier $metier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MetierType::class, $metier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_metier_show', ['id' => $metier->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres/metier/edit.html.twig', [
            'metier' => $metier,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_metier_delete', methods: ['POST'])]
    #[IsGranted('EDIT', 'metier')]
    public function delete(Request $request, Metier $metier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$metier->getId(), (string) $request->getPayload()->get('_token'))) {
            $entityManager->remove($metier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_metier_index', [], Response::HTTP_SEE_OTHER);
    }
}
