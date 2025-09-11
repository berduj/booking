<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TypeHeadAutocomplete;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', []);
    }

    #[Route('/typehead', name: 'typehead_autocomplete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function typehead(Request $request, TypeHeadAutocomplete $autocomplete): JsonResponse
    {
        $query = (string) $request->request->get('query');

        return new JsonResponse($autocomplete->get($query));
    }
}
