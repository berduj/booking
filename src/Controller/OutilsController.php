<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\SecurityLog;
use App\Entity\Structure;
use App\Repository\PersonneRepository;
use App\Repository\SecurityLogRepository;
use App\Repository\StructureRepository;
use App\Service\Tools\Merge\PersonneMergeableFinder;
use App\Service\Tools\Merge\PersonneMerger;
use App\Service\Tools\Merge\StructureMergeableFinder;
use App\Service\Tools\Merge\StructureMerger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/outils')]
#[IsGranted('ROLE_ADMIN')]
class OutilsController extends AbstractController
{
    #[Route('/merge-structures', name: 'app_merge_structure')]
    public function mergeStructure(
        StructureRepository $structureRepository,
        Request $request,
        StructureMergeableFinder $finder,
        StructureMerger $merger,
    ): Response {
        if ($request->isMethod('POST')) {
            try {
                $delete = $structureRepository->find($request->get('delete'));
                if (!$delete instanceof Structure) {
                    throw new \InvalidArgumentException('Structure à supprimer non trouvée');
                }

                $keep = $structureRepository->find($request->get('keep'));
                if (!$keep instanceof Structure) {
                    throw new \InvalidArgumentException('Structure à conserver non trouvée');
                }

                $merger->merge($keep, $delete);
                $this->addFlash('success', 'Structures fusionnées');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('outils/mergeStructure.html.twig', [
            'structures' => $finder->findDeletable(),
            'finder' => $finder,
        ]);
    }

    #[Route('/merge-personnes', name: 'app_merge_personne')]
    public function mergePersonne(
        PersonneRepository $personneRepository,
        Request $request,
        PersonneMergeableFinder $finder,
        PersonneMerger $merger,
    ): Response {
        if ($request->isMethod('POST')) {
            try {
                $delete = $personneRepository->find($request->get('delete'));
                if (!$delete instanceof Personne) {
                    throw new \InvalidArgumentException('Personne à supprimer non trouvée');
                }

                $keep = $personneRepository->find($request->get('keep'));
                if (!$keep instanceof Personne) {
                    throw new \InvalidArgumentException('Personne à conserver non trouvée');
                }
                $merger->merge($keep, $delete);
                $this->addFlash('success', 'Personnes fusionnées');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('outils/mergePersonne.html.twig', [
            'personnes' => $finder->findDeletable(),
            'finder' => $finder,
        ]);
    }

    #[Route('/secutiry-log', name: 'app_security_log', defaults: ['page' => 1])]
    #[Route('/secutiry-log/{page}', name: 'app_security_log_page')]
    public function securityLog(SecurityLogRepository $repository, int $page): Response
    {
        $securityLogs = $repository->findByPage($page, SecurityLog::LOGIN);

        return $this->render('outils/securityLog.html.twig', [
            'page' => $page,
            'nbPages' => $repository->getNbPages(SecurityLog::LOGIN),
            'nbParPages' => SecurityLog::NB_PER_PAGE_LOGIN,
            'total' => $repository->getNbEnregistrements(SecurityLog::LOGIN),
            'securityLogs' => $securityLogs,
        ]);
    }
}
