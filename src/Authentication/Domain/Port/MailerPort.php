<?php

namespace App\Authentication\Domain\Port;

use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\TokenValue;

interface MailerPort
{
    public function sendValidationEmail(Email $to, TokenValue $token): void;

    public function sendPasswordResetEmail(Email $to, TokenValue $token): void;
}
