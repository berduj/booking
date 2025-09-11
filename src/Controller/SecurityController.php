<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\ChangePasswordEvent;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        /*
         * @deprecated Pour eviter le Pb de CSRF après le changement de password @todo : à supprimer
         */
        if ($request->isMethod('GET')) {
            $request->getSession()->invalidate();
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/showMe', name: 'app_showme')]
    public function showMe(): Response
    {
        return $this->render('security/showMe.html.twig', ['user' => $this->getUser()]);
    }

    #[Route('/changeMyPassword}', name: 'app_change_my_password', methods: ['GET', 'POST'])]
    public function myPassword(Request $request, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher): Response
    {
        $form = $this->createForm(ChangePasswordFormType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            if ($this->getUser() instanceof UserInterface) {
                $eventDispatcher->dispatch(new ChangePasswordEvent($this->getUser()));
            }

            return $this->redirectToRoute('app_showme', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('security/edit.html.twig', [
            'user' => $this->getUser(),
            'form' => $form,
        ]);
    }
}
