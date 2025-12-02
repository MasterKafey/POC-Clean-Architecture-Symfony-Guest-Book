<?php

namespace App\Authentication\Infrastructure\Symfony\Adapter;

use App\Authentication\Domain\Port\MailerPort;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\TokenValue;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as Mail;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final readonly class MailerAdapter implements MailerPort
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment     $environment,
        private RouterInterface $router
    )
    {

    }

    public function sendValidationEmail(Email $to, TokenValue $token): void
    {
        $template = $this->environment->load('Mail/Authentication/send-validation-email.html.twig');

        $url = $this
            ->router
            ->generate('app_authentication_infrastructure_symfony_user_validate_email', ['token' => $token->value()], UrlGeneratorInterface::ABSOLUTE_URL);

        $mail = new Mail();
        $mail
            ->to($to)
            ->html($template->renderBlock('html', [
                'url' => $url,
            ]))
            ->text($template->renderBlock('text', [
                'url' => $url,
            ]));

        $this->mailer->send($mail);
    }

    public function sendPasswordResetEmail(Email $to, TokenValue $token): void
    {

    }
}
