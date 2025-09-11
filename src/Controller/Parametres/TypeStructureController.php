<?php

declare(strict_types=1);

namespace App\Controller\Parametres;

use App\Entity\TypeStructure;
use App\Form\TypeStructureType;
use App\Repository\TypeStructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parametres/typeStructure')]
#[IsGranted('ROLE_PARAMETRAGE')]
class TypeStructureController extends AbstractController
{
    #[Route('/', name: 'app_typeStructure_index', methods: ['GET'])]
    public function index(TypeStructureRepository $typeStructureRepository): Response
    {
        return $this->render('parametres/typeStructure/index.html.twig', [
            'typeStructures' => $typeStructureRepository->findBy([], ['sortable' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_typeStructure_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeStructure = new TypeStructure();
        $form = $this->createForm(TypeStructureType::class, $typeStructure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeStructure);
            $entityManager->flush();

            return $this->redirectToRoute('app_typeStructure_show', ['id' => $typeStructure->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('parametres/typeStructure/new.html.twig', [
                'typeStructure' => $typeStructure,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_typeStructure_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'typeStructure')]
    public function show(TypeStructure $typeStructure): Response
    {
        return $this->render('parametres/typeStructure/show.html.twig', [
            'typeStructure' => $typeStructure,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_typeStructure_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'typeStructure')]
    public function edit(Request $request, TypeStructure $typeStructure, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeStructureType::class, $typeStructure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_typeStructure_show', ['id' => $typeStructure->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres/typeStructure/edit.html.twig', [
            'typeStructure' => $typeStructure,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_typeStructure_delete', methods: ['POST'])]
    #[IsGranted('EDIT', 'typeStructure')]
    public function delete(Request $request, TypeStructure $typeStructure, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeStructure->getId(), (string) $request->getPayload()->get('_token'))) {
            $entityManager->remove($typeStructure);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_typeStructure_index', [], Response::HTTP_SEE_OTHER);
    }
}
