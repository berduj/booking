<?php

declare(strict_types=1);

namespace App\Controller\Contact;

use App\Entity\Contact;
use App\Entity\ContactInterlocuteur;
use App\Entity\Personne;
use App\Entity\Structure;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Service\Persister\YearMonthPersister;
use App\ValueObject\YearMonth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contact')]
#[IsGranted('ROLE_CONTACT_VIEW')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'app_contact_index', methods: ['GET'], defaults: ['month' => null, 'year' => null])]
    #[Route('/date/{month}/{year}', name: 'app_contact_index_date', methods: ['GET'])]
    public function index(?int $month, ?int $year, ContactRepository $contactRepository, YearMonthPersister $yearMonthPersister): Response
    {
        if ($month !== null and $year !== null) {
            $yearMonthPersister->set(new YearMonth($year, $month), 'contacts');
        }
        $yearMonth = $yearMonthPersister->get('contacts');

        return $this->render('contact/index.html.twig', [
            'contacts' => $contactRepository->findByYearMonth($yearMonth),
            'yearMonth' => $yearMonth,
        ]);
    }

    #[Route('/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    #[Route('/new-structure/{structure}', name: 'app_contact_structure_new', methods: ['GET', 'POST'])]
    #[Route('/new-personne/{personne}', name: 'app_contact_personne_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONTACT_CREATE')]
    public function new(Request $request, EntityManagerInterface $entityManager, ?Structure $structure, ?Personne $personne): Response
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $contact = new Contact($this->getUser());
        $contact->mainStructure = $structure;
        $contact->mainPersonne = $personne;

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);

            if ($structure) {
                if ($form->has('personnes') and $form->get('personnes')->getData()->count() > 0) {
                    foreach ($form->get('personnes')->getData() as $personne) {
                        $contactInterlocuteur = new ContactInterlocuteur($contact, $personne, $structure);
                        $entityManager->persist($contactInterlocuteur);
                    }
                } else {
                    $contactInterlocuteur = new ContactInterlocuteur($contact, null, $structure);
                    $entityManager->persist($contactInterlocuteur);
                }
                $entityManager->flush();

                return $this->redirectToRoute('app_structure_show', ['id' => $structure->getId(), 'tab' => 'contacts'], Response::HTTP_SEE_OTHER);
            }

            if ($personne) {
                if ($form->has('structures') and $form->get('structures')->getData()->count() > 0) {
                    foreach ($form->get('structures')->getData() as $structure) {
                        $contactInterlocuteur = new ContactInterlocuteur($contact, $personne, $structure);
                        $entityManager->persist($contactInterlocuteur);
                    }
                } else {
                    $contactInterlocuteur = new ContactInterlocuteur($contact, $personne, null);
                    $entityManager->persist($contactInterlocuteur);
                }

                $entityManager->flush();

                return $this->redirectToRoute('app_personne_show', ['id' => $personne->getId(), 'tab' => 'contacts'], Response::HTTP_SEE_OTHER);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_contact_show', ['id' => $contact->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('contact/new.html.twig', [
                'contact' => $contact,
                'structure' => $structure,
                'personne' => $personne,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_contact_show', methods: ['GET', 'POST'])]
    #[IsGranted('VIEW', 'contact')]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', ['contact' => $contact]);
    }

    #[Route('/edit/{id}', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'contact')]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_show', ['id' => $contact->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/delete/delete/{id}', name: 'app_contact_delete', methods: ['POST'])]
    #[IsGranted('EDIT', 'contact')]
    public function delete(
        Request $request,
        Contact $contact,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), (string) $request->getPayload()->get('_token'))) {
            try {
                $entityManager->remove($contact);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('app_contact_show', ['id' => $contact->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/view-document/{contact}', name: 'app_contact_view_document', methods: ['GET'])]
    #[IsGranted('VIEW', 'contact')]
    public function viewDocument(Contact $contact, EntityManagerInterface $entityManager, string $uploadDir): Response
    {
        $filePath = $contact->getFilepath();

        return new BinaryFileResponse($uploadDir.$filePath);
    }
}
