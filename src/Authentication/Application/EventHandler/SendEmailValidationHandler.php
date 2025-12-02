<?php

namespace App\Authentication\Application\EventHandler;

use App\Authentication\Application\UseCase\Token\CreateToken\CreateTokenCommand;
use App\Authentication\Application\UseCase\Token\CreateToken\CreateTokenHandler;
use App\Authentication\Domain\Enum\TokenType;
use App\Authentication\Domain\Event\UserRegistered;
use App\Authentication\Domain\Port\MailerPort;

final readonly class SendEmailValidationHandler
{
    public function __construct(
        private MailerPort         $mailerPort,
        private CreateTokenHandler $handler,
    )
    {

    }

    public function __invoke(UserRegistered $event): void
    {
        $command = new CreateTokenCommand(
            $event->userId(),
            TokenType::REGISTRATION
        );


        $token = ($this->handler)($command);

        $this->mailerPort->sendValidationEmail($event->email(), $token->getValue());
    }
}
