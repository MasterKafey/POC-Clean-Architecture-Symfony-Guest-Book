<?php

namespace App\Comment\Application\UseCase\Comment\UnblockComment;

use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Comment\Domain\Service\CommentPolicy;
use App\Comment\Domain\Event\CommentBlocked;
use App\Comment\Domain\Event\CommentUnblocked;
use App\Comment\Domain\Exception\CommentNotBlockedException;
use App\Comment\Domain\Exception\CommentNotFoundException;
use App\Comment\Domain\Exception\UnauthorizedException;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Shared\Domain\Port\EventBusPort;

final readonly class UnblockCommentHandler
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UserRepositoryInterface    $userRepository,
        private EventBusPort               $eventBus
    )
    {

    }

    public function __invoke(
        UnblockCommentCommand $command,
    ): void
    {
        $comment = $this->commentRepository->findById($command->getCommentId());

        if (null === $comment) {
            throw new CommentNotFoundException($command->getCommentId());
        }

        $user = $this->userRepository->findById($command->getInitiatorId());

        if (!$comment->isBlocked()) {
            throw new CommentNotBlockedException($comment);
        }

        if (!CommentPolicy::canToggleBlock($comment, $user)) {
            throw new UnauthorizedException(sprintf("User '%s' can't unblock comment with id '%s'.", $user->getEmail(), $comment->getId()));
        }

        $comment->setBlocked(false);

        $this->commentRepository->save($comment);

        $this->eventBus->dispatch(new CommentUnblocked(
            $comment->getUserId(),
            $user->getId(),
            $user->getEmail(),
            $comment->getId(),
            $comment->getMessage()
        ));
    }
}
