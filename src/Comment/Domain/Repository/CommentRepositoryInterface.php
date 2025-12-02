<?php

namespace App\Comment\Domain\Repository;

use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\ValueObject\CommentId;
use App\Shared\Domain\ValueObject\Pagination;

interface CommentRepositoryInterface
{
    public function save(Comment $commentDomain): Comment;

    /** @return Comment[] */
    public function findAll(Pagination $pagination): array;

    public function findNotBlocked(Pagination $pagination): array;

    public function findById(CommentId $id): ?Comment;

    public function findByUserId(UserId $userId): ?Comment;

    public function delete(CommentId $id): void;
}
