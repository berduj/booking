<?php

declare(strict_types=1);

namespace App\Service\Email;

use App\Entity\Personne;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class InfoCompteEmail
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly RouterInterface $router,
    ) {
    }

    public function send(Personne $user): void
    {
        $software = $_ENV['SOFTWARE_NAME'];

        $email = new TemplatedEmail();
        $email->from(new Address($_ENV['MAILER_SENDER'], $_ENV['MAILER_SENDER_NAME']));
        if ($_ENV['APP_ENV'] === 'prod' && $user->getEmail()) {
            $email->to($user->getEmail());
        } else {
            $email->to('berduj@gmail.com');
        }
        $email->subject("Informations d'accès à $software");
        $email->htmlTemplate('email/infoCompte.html.twig');
        $email->context([
            'user' => $user,
            'url' => $this->router->generate('app_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'software' => $software,
        ]);

        $this->mailer->send($email);
    }
}
