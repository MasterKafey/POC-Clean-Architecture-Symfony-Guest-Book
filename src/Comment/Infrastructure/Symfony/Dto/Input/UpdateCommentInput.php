<?php

namespace App\Comment\Infrastructure\Symfony\Dto\Input;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateCommentInput
{
    public function __construct(
        #[Assert\Length(min: 10, max: 10000)]
        #[Assert\NotBlank]
        public ?string $message = null,
    )
    {

    }
}
