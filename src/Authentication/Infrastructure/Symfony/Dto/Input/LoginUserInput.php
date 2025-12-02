<?php

namespace App\Authentication\Infrastructure\Symfony\Dto\Input;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class LoginUserInput
{
    #[Assert\NotBlank]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $password = null;

    public static function fromRequest(Request $request): self
    {
        $data = $request->toArray();

        $dto = new self();
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;

        return $dto;
    }
}
