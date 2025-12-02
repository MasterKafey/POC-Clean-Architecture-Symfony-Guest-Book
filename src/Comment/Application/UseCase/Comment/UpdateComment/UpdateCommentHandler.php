<?php

namespace App\Comment\Application\UseCase\Comment\UpdateComment;

use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Comment\Domain\Service\CommentPolicy;
use App\Comment\Domain\Event\CommentUpdated;
use App\Comment\Domain\Exception\CommentNotFoundException;
use App\Comment\Domain\Exception\UnauthorizedException;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Comment\Infrastructure\Symfony\Dto\Output\CommentOutput;
use App\Shared\Domain\Port\EventBusPort;

final readonly class UpdateCommentHandler
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UserRepositoryInterface    $userRepository,
        private EventBusPort               $eventBus
    )
    {

    }

    public function __invoke(
        UpdateCommentCommand $command
    ): UpdateCommentResult
    {
        $comment = $this->commentRepository->findById($command->getId());

        if (null === $comment) {
            throw new CommentNotFoundException($command->getId());
        }

        $user = $this->userRepository->findById($command->getUserId());

        if (!CommentPolicy::canUpdate($comment, $user)) {
            throw new UnauthorizedException(sprintf(
                "User '%s' cannot update comment with id '%s'",
                $comment->getUserId(),
                $comment->getId()
            ));
        }
        $oldMessage = $comment->getMessage();
        $comment->setMessage($command->getMessage());
        $this->commentRepository->save($comment);
        $newMessage = $comment->getMessage();

        $this->eventBus->dispatch(new CommentUpdated(
            $comment->getId(),
            $user->getId(),
            $user->getEmail(),
            $oldMessage,
            $newMessage
        ));

        return new UpdateCommentResult($comment);
    }
}
