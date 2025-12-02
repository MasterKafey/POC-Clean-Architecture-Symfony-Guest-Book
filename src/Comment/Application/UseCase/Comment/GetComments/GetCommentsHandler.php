<?php

namespace App\Comment\Application\UseCase\Comment\GetComments;

use App\Comment\Domain\Repository\CommentRepositoryInterface;

final readonly class GetCommentsHandler
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository
    )
    {

    }

    public function __invoke(
        GetCommentsQuery $query,
    ): GetCommentsResult
    {
        $comments = $query->getIncludeBlocked() ?
            $this->commentRepository->findAll($query->getPagination()) :
            $this->commentRepository->findNotBlocked($query->getPagination());

        return new GetCommentsResult($comments);
    }
}
