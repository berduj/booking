<?php

declare(strict_types=1);

namespace App\Controller\Parametres;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parametres/tag')]
#[IsGranted('ROLE_PARAMETRAGE')]
class TagController extends AbstractController
{
    #[Route('/', name: 'app_tag_index', methods: ['GET'])]
    public function index(TagRepository $tagRepository, Request $request): Response
    {
        foreach (Tag::TYPES as $type) {
            $tags[$type] = [];
        }
        foreach ($tagRepository->findAll() as $tag) {
            $tags[$tag->getType()][] = $tag;
        }

        return $this->render('parametres/tag/index.html.twig', [
            'tags' => $tags,
            'types' => Tag::TYPES,
            'tab' => $request->get('tab', Tag::TYPES[0]),
        ]);
    }

    #[Route('/new/{type}', name: 'app_tag_new', methods: ['GET', 'POST'])]
    public function new(string $type, Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = new Tag($type);
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app_tag_show', ['id' => $tag->getId()], Response::HTTP_SEE_OTHER);
        }

        return
            $this->render('parametres/tag/new.html.twig', [
                'tag' => $tag,
                'form' => $form,
            ]);
    }

    #[Route('/show/{id}', name: 'app_tag_show', methods: ['GET'])]
    #[IsGranted('VIEW', 'tag')]
    public function show(Tag $tag): Response
    {
        return $this->render('parametres/tag/show.html.twig', [
            'tag' => $tag,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_tag_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', 'tag')]
    public function edit(Request $request, Tag $tag, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tag_show', ['id' => $tag->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parametres/tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_tag_delete', methods: ['POST'])]
    #[IsGranted('EDIT', 'tag')]
    public function delete(Request $request, Tag $tag, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), (string) $request->getPayload()->get('_token'))) {
            $entityManager->remove($tag);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
