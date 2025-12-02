<?php

namespace App\Authentication\Infrastructure\Symfony\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

final class RegisterUserInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ?string $firstName = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ?string $lastName = null,

        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email = null,

        #[Assert\NotBlank]
        #[Assert\Length(min: 6)]
        public ?string $password = null,
    )
    {

    }
}
