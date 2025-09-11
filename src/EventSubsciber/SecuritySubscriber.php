<?php

declare(strict_types=1);

namespace App\EventSubsciber;

use App\Entity\Personne;
use App\Entity\SecurityLog;
use App\Event\ChangePasswordEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SecuritySubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onLogin',
            ChangePasswordEvent::class => 'onChangePassword',
        ];
    }

    public function onLogin(InteractiveLoginEvent $event): void
    {
        $personne = $event->getAuthenticationToken()->getUser();
        if ($personne instanceof Personne) {
            $personne->setLastLogin(new \DateTime());

            $securityLog = new SecurityLog($personne, new \DateTime(), SecurityLog::LOGIN);
            $this->entityManager->persist($securityLog);
            $this->entityManager->flush();
        }
    }

    public function onChangePassword(ChangePasswordEvent $event): void
    {
        $personne = $event->getUser();
        if ($personne instanceof Personne) {
            $securityLog = new SecurityLog($personne, new \DateTime(), SecurityLog::CHANGE_PASSWORD);
            $this->entityManager->persist($securityLog);
            $this->entityManager->flush();
        }
    }
}
