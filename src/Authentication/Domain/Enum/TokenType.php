<?php

namespace App\Authentication\Domain\Enum;

enum TokenType: string
{
    case REGISTRATION = 'registration';
    case AUTHENTICATION = 'authentication';
    case FORGOT_PASSWORD = 'forgot-password';
}
