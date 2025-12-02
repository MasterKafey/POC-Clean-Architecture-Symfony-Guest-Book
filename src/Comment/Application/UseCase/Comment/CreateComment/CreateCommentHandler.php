<?php

namespace App\Comment\Application\UseCase\Comment\CreateComment;

use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\Event\CommentCreated;
use App\Comment\Domain\Exception\UserCommentAlreadyExists;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Comment\Domain\ValueObject\CommentId;
use App\Shared\Domain\Port\EventBusPort;
use App\Shared\Domain\Port\UuidGeneratorPort;

final readonly class CreateCommentHandler
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UuidGeneratorPort          $uuidGenerator,
        private EventBusPort               $eventBus
    )
    {

    }

    public function __invoke(
        CreateCommentCommand $command
    ): void
    {
        $comment = $this->commentRepository->findByUserId($command->getUserId());

        if (null !== $comment) {
            throw new UserCommentAlreadyExists($command->getUserId());
        }

        $comment = new Comment(
            new CommentId($this->uuidGenerator->generate()),
            $command->getUserId(),
            $command->getMessage(),
            new \DateTimeImmutable(),
        );

        $comment = $this->commentRepository->save($comment);

        $this->eventBus->dispatch(new CommentCreated(
            $comment->getId(),
            $command->getUserId(),
            $command->getMessage(),
        ));
    }
}
