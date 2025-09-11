<?php

declare(strict_types=1);

namespace App\Controller\Alerte;

use App\Entity\Alerte;
use App\Entity\Personne;
use App\Form\AlerteType;
use App\Repository\AlerteRepository;
use App\Service\Persister\YearMonthPersister;
use App\ValueObject\YearMonth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/alerte')]
#[IsGranted('ROLE_USER')]
class AlerteController extends AbstractController
{
    #[Route('/', name: 'app_alerte_index', methods: ['GET', 'POST'], defaults: ['month' => null, 'year' => null])]
    #[Route('/date/{month}/{year}', name: 'app_alerte_index_date', methods: ['GET', 'POST'])]
    public function index(
        ?int $month,
        ?int $year,
        AlerteRepository $alerteRepository,
        YearMonthPersister $yearMonthPersister,
    ): Response {
        if ($month !== null and $year !== null) {
            $yearMonthPersister->set(new YearMonth($year, $month), 'alertes');
        }
        $yearMonth = $yearMonthPersister->get('alertes');

        /** @var Personne $user */
        $user = $this->getUser();

        return $this->render('alerte/index.html.twig', [
            'alertes' => $alerteRepository->findByYearMonthFiltreUser($yearMonth, $user),
            'yearMonth' => $yearMonth,
        ]);
    }

    #[Route('/new', name: 'app_alerte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Personne $user */
        $user = $this->getUser();
        $alerte = new Alerte($user, $user);

        $form = $this->createForm(AlerteType::class, $alerte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($alerte);
            $entityManager->flush();

            return $this->redirectToRoute('app_alerte_show', ['id' => $alerte->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('alerte/new.html.twig', [
                'alerte' => $alerte,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_alerte_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'alerte')]
    public function show(Alerte $alerte): Response
    {
        return $this->render('alerte/show.html.twig', [
            'alerte' => $alerte,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_alerte_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'alerte')]
    public function edit(Request $request, Alerte $alerte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AlerteType::class, $alerte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_alerte_show', ['id' => $alerte->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('alerte/edit.html.twig', [
            'alerte' => $alerte,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_alerte_delete', methods: ['POST'])]
    #[IsGranted('DELETE', 'alerte')]
    public function delete(
        Request $request,
        Alerte $alerte,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$alerte->getId(), (string) $request->getPayload()->get('_token'))) {
            try {
                $entityManager->remove($alerte);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('app_alerte_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->redirectToRoute('app_alerte_index', [], Response::HTTP_SEE_OTHER);
    }
}
