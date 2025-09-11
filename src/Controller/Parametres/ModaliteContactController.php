<?php

declare(strict_types=1);

namespace App\Controller\Parametres;

use App\Entity\ModaliteContact;
use App\Form\ModaliteContactType;
use App\Repository\ModaliteContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parametres/modaliteContact')]
#[IsGranted('ROLE_PARAMETRAGE')]
class ModaliteContactController extends AbstractController
{
    #[Route('/', name: 'app_modaliteContact_index', methods: ['GET'])]
    public function index(ModaliteContactRepository $modaliteContactRepository): Response
    {
        return $this->render('parametres/modaliteContact/index.html.twig', [
            'modaliteContacts' => $modaliteContactRepository->findBy([], ['sortable' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_modaliteContact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $modaliteContact = new ModaliteContact();
        $form = $this->createForm(ModaliteContactType::class, $modaliteContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($modaliteContact);
            $entityManager->flush();

            return $this->redirectToRoute('app_modaliteContact_show', ['id' => $modaliteContact->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('parametres/modaliteContact/new.html.twig', [
                'modaliteContact' => $modaliteContact,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_modaliteContact_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'modaliteContact')]
    public function show(ModaliteContact $modaliteContact): Response
    {
        return $this->render('parametres/modaliteContact/show.html.twig', [
            'modaliteContact' => $modaliteContact,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_modaliteContact_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'modaliteContact')]
    public function edit(Request $request, ModaliteContact $modaliteContact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ModaliteContactType::class, $modaliteContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_modaliteContact_show', ['id' => $modaliteContact->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres/modaliteContact/edit.html.twig', [
            'modaliteContact' => $modaliteContact,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_modaliteContact_delete', methods: ['POST'])]
    #[IsGranted('EDIT', 'modaliteContact')]
    public function delete(Request $request, ModaliteContact $modaliteContact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$modaliteContact->getId(), (string) $request->getPayload()->get('_token'))) {
            $entityManager->remove($modaliteContact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_modaliteContact_index', [], Response::HTTP_SEE_OTHER);
    }
}
