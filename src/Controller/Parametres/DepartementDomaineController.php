<?php

declare(strict_types=1);

namespace App\Controller\Parametres;

use App\Entity\DepartementDomaine;
use App\Form\DepartementDomaineType;
use App\Repository\DepartementDomaineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parametres/departementDomaine')]
#[IsGranted('ROLE_PARAMETRAGE')]
class DepartementDomaineController extends AbstractController
{
    #[Route('/', name: 'app_departementDomaine_index', methods: ['GET'])]
    public function index(DepartementDomaineRepository $departementDomaineRepository): Response
    {
        return $this->render('parametres/departementDomaine/index.html.twig', [
            'departementDomaines' => $departementDomaineRepository->findBy([], ['sortable' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_departementDomaine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $departementDomaine = new DepartementDomaine();
        $form = $this->createForm(DepartementDomaineType::class, $departementDomaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($departementDomaine);
            $entityManager->flush();

            return $this->redirectToRoute('app_departementDomaine_show', ['id' => $departementDomaine->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('parametres/departementDomaine/new.html.twig', [
                'departementDomaine' => $departementDomaine,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_departementDomaine_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'departementDomaine')]
    public function show(DepartementDomaine $departementDomaine): Response
    {
        return $this->render('parametres/departementDomaine/show.html.twig', [
            'departementDomaine' => $departementDomaine,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_departementDomaine_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'departementDomaine')]
    public function edit(Request $request, DepartementDomaine $departementDomaine, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartementDomaineType::class, $departementDomaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_departementDomaine_show', ['id' => $departementDomaine->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres/departementDomaine/edit.html.twig', [
            'departementDomaine' => $departementDomaine,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_departementDomaine_delete', methods: ['POST'])]
    #[IsGranted('EDIT', 'departementDomaine')]
    public function delete(Request $request, DepartementDomaine $departementDomaine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$departementDomaine->getId(), (string) $request->getPayload()->get('_token'))) {
            $entityManager->remove($departementDomaine);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_departementDomaine_index', [], Response::HTTP_SEE_OTHER);
    }
}
