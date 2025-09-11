<?php

declare(strict_types=1);

namespace App\Controller\Parametres;

use App\Entity\Export;
use App\Form\ExportType;
use App\Repository\ExportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parametres/export')]
#[IsGranted('ROLE_PARAMETRAGE')]
class ExportController extends AbstractController
{
    #[Route('/', name: 'app_export_index', methods: ['GET'])]
    public function index(ExportRepository $exportRepository): Response
    {
        return $this->render('parametres/export/index.html.twig', [
            'exports' => $exportRepository->findBy([], ['sortable' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_export_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $export = new Export();
        $form = $this->createForm(ExportType::class, $export);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($export);
            $entityManager->flush();

            return $this->redirectToRoute('app_export_show', ['id' => $export->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('parametres/export/new.html.twig', [
                'export' => $export,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_export_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'export')]
    public function show(Export $export): Response
    {
        return $this->render('parametres/export/show.html.twig', [
            'export' => $export,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_export_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'export')]
    public function edit(Request $request, Export $export, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExportType::class, $export);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_export_show', ['id' => $export->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres/export/edit.html.twig', [
            'export' => $export,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_export_delete', methods: ['POST'])]
    #[IsGranted('EDIT_CODE', 'export')]
    public function delete(Request $request, Export $export, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$export->getId(), (string) $request->getPayload()->get('_token'))) {
            $entityManager->remove($export);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_export_index', [], Response::HTTP_SEE_OTHER);
    }
}
