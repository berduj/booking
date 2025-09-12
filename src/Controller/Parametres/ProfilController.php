<?php

declare(strict_types=1);

namespace App\Controller\Parametres;

use App\Entity\Profil;
use App\Form\ProfilType;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parametres/profil')]
#[IsGranted('ROLE_ADMIN')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profil_index', methods: ['GET'])]
    public function index(ProfilRepository $profilRepository): Response
    {
        return $this->render('parametres/profil/index.html.twig', [
            'profils' => $profilRepository->findBy([], ['sortable' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_profil_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $profil = new Profil();
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil_show', ['id' => $profil->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('parametres/profil/new.html.twig', [
                'profil' => $profil,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_profil_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'profil')]
    public function show(Profil $profil): Response
    {
        return $this->render('parametres/profil/show.html.twig', [
            'profil' => $profil,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'profil')]
    public function edit(Request $request, Profil $profil, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($profil->getId() === 1 && !in_array('ROLE_SUPER_ADMIN', $profil->getRoles(), true)) {
                $profil->setRoles(array_merge($profil->getRoles(), ['ROLE_SUPER_ADMIN']));
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_profil_show', ['id' => $profil->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres/profil/edit.html.twig', [
            'profil' => $profil,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_profil_delete', methods: ['POST'])]
    #[IsGranted('EDIT', 'profil')]
    public function delete(Request $request, Profil $profil, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profil->getId(), (string) $request->getPayload()->get('_token'))) {
            $entityManager->remove($profil);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
    }
}
