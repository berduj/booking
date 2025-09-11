<?php

declare(strict_types=1);

namespace App\Controller\Contact;

use App\Entity\Contact;
use App\Entity\ContactInterlocuteur;
use App\Entity\Personne;
use App\Entity\Structure;
use App\Repository\PersonneRepository;
use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contact')]
#[IsGranted('ROLE_CONTACT_VIEW')]
class ContactInterlocuteurController extends AbstractController
{
    #[Route('/autocomplete-interlocuteur', name: 'app_contact_interlocuteur_autocomplete', methods: ['POST'])]
    public function referentAutocomplete(Request $request, PersonneRepository $personneRepository, StructureRepository $structureRepository): JsonResponse
    {
        $query = (string) $request->request->get('query');
        $ret = array_merge(
            $personneRepository->findAutocomplete($query, 8),
            $structureRepository->findAutocomplete($query, 8)
        );

        return new JsonResponse($ret);
    }

    #[Route('/add-interlocuteur', name: 'app_contact_add_interlocuteur', methods: ['POST'])]
    public function addInterlocuteur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $type = $request->request->get('type');
        $interlocuteur = $request->request->get('interlocuteur');
        $contact = $entityManager->getRepository(Contact::class)->find($request->request->get('contact'));
        if (!$contact instanceof Contact) {
            throw new \Exception('Contact non trouvé');
        }

        if ($type === 'personne-picto') {
            $structure = null;
            $personne = $entityManager->getRepository(Personne::class)->find($interlocuteur);
        } else {
            $personne = null;
            $structure = $entityManager->getRepository(Structure::class)->find($interlocuteur);
        }

        try {
            if (!$this->isGranted('EDIT', $contact)) {
                throw new \Exception("Vous n'avez pas les droits nécessaires");
            }
            if ($personne === null && $structure === null) {
                throw new \Exception('Il faut indiquer une personne ou une structure');
            }

            $contactInterlocuteur = new ContactInterlocuteur($contact, $personne, $structure);
            $entityManager->persist($contactInterlocuteur);
            $entityManager->flush();
            $this->addFlash('success', 'Interlocuteur ajoué');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Ajout impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_contact_show', ['id' => $contact->getId(), 'tab' => 'interlocuteurs'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete-interlocuteur/{id}/{contact}', name: 'app_contact_remove_interlocuteur', methods: ['GET'])]
    #[IsGranted('EDIT', 'contact')]
    public function removeInterlocuteur(ContactInterlocuteur $contactInterlocuteur, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->remove($contactInterlocuteur);
            $entityManager->flush();
            $this->addFlash('success', 'Le interlocuteur  a été supprimé(e) de '.$contact);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Suppression impossible : '.$e->getMessage());
        }

        return $this->redirectToRoute('app_contact_show', ['id' => $contact->getId(), 'tab' => 'interlocuteurs'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/edit-interlocuteur-structure', name: 'app_contact_edit_interlocuteur_structure', methods: ['POST'])]
    public function editContactInterlocuteurStructure(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $contactInterlocuteur = $entityManager->getRepository(ContactInterlocuteur::class)->find($request->request->get('id'));
            if (!$contactInterlocuteur instanceof ContactInterlocuteur) {
                throw new \Exception('Contact interlocuteur non trouvé');
            }
            $structure = $entityManager->find(Structure::class, $request->request->get('value'));

            if (!$this->isGranted('EDIT', $contactInterlocuteur->getContact())) {
                throw new \Exception("Vous n'avez pas les droits");
            }
            $contactInterlocuteur->setStructure($structure);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['status' => false, 'message' => 'MAJ impossible : '.$e->getMessage()]);
        }

        return new JsonResponse([
            'status' => true,
            'message' => 'MAJ OK',
        ]);
    }

    #[Route('/edit-interlocuteur-personne', name: 'app_contact_edit_interlocuteur_personne', methods: ['POST'])]
    public function editContactInterlocuteurPersonne(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $contactInterlocuteur = $entityManager->getRepository(ContactInterlocuteur::class)->find($request->request->get('id'));
            if (!$contactInterlocuteur instanceof ContactInterlocuteur) {
                throw new \Exception('Contact interlocuteur non trouvé');
            }
            $personne = $entityManager->find(Personne::class, $request->request->get('value'));

            if (!$this->isGranted('EDIT', $contactInterlocuteur->getContact())) {
                throw new \Exception("Vous n'avez pas les droits");
            }
            $contactInterlocuteur->setPersonne($personne);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['status' => false, 'message' => 'MAJ impossible : '.$e->getMessage()]);
        }

        return new JsonResponse([
            'status' => true,
            'message' => 'MAJ OK',
        ]);
    }
}
