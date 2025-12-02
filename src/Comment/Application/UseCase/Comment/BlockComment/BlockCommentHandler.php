<?php

namespace App\Comment\Application\UseCase\Comment\BlockComment;

use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Comment\Domain\Service\CommentPolicy;
use App\Comment\Domain\Event\CommentBlocked;
use App\Comment\Domain\Exception\CommentAlreadyBlockedException;
use App\Comment\Domain\Exception\CommentNotFoundException;
use App\Comment\Domain\Exception\UnauthorizedException;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Shared\Domain\Port\EventBusPort;

final readonly class BlockCommentHandler
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UserRepositoryInterface    $userRepository,
        private EventBusPort               $eventBus,
    )
    {

    }

    public function __invoke(
        BlockCommentCommand $command,
    ): void
    {
        $comment = $this->commentRepository->findById($command->getCommentId());

        if (null === $comment) {
            throw new CommentNotFoundException($command->getCommentId());
        }

        $user = $this->userRepository->findById($command->getInitiatorId());

        if ($comment->isBlocked()) {
            throw new CommentAlreadyBlockedException($comment);
        }

        if (!CommentPolicy::canToggleBlock($comment, $user)) {
            throw new UnauthorizedException(sprintf("User '%s' can't block comment with id '%s'", $user->getEmail(), $user->getId()));
        }

        $comment->setBlocked(true);

        $this->commentRepository->save($comment);

        $this->eventBus->dispatch(
            new CommentBlocked(
                $comment->getId(),
                $comment->getMessage(),
                $user->getId(),
                $user->getEmail()
            )
        );
    }
}
