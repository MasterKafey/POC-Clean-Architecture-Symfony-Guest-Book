<?php

namespace App\Comment\Application\UseCase\Comment\DeleteComment;

use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Comment\Domain\Service\CommentPolicy;
use App\Comment\Domain\Event\CommentDeleted;
use App\Comment\Domain\Exception\CommentNotFoundException;
use App\Comment\Domain\Exception\UnauthorizedException;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Shared\Domain\Port\EventBusPort;

final readonly class DeleteCommentHandler
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UserRepositoryInterface    $userRepository,
        private EventBusPort               $eventBus
    )
    {

    }

    public function __invoke(
        DeleteCommentCommand $command
    ): void
    {
        $comment = $this->commentRepository->findById($command->commentId());

        if (null === $comment) {
            throw new CommentNotFoundException($command->commentId());
        }

        $user = $this->userRepository->findById($command->userId());

        if (null === $user) {
            throw new UserNotFoundException($command->userId());
        }

        if (!CommentPolicy::canDelete($comment, $user)) {
            throw new UnauthorizedException(sprintf("User '%s' is not authorized to delete the comment with id '%s'", $user->getEmail(), $comment->getId()));
        }

        $event = new CommentDeleted(
            $comment->getId(),
            $comment->getUserId(),
            $user->getId(),
            $comment->getMessage(),
        );
        $this->commentRepository->delete($comment->getId());

        $this->eventBus->dispatch($event);
    }
}
