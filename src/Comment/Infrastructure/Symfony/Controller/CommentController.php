<?php

namespace App\Comment\Infrastructure\Symfony\Controller;

use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\UserId;
use App\Authentication\Infrastructure\Doctrine\Entity\User;
use App\Comment\Application\UseCase\Comment\BlockComment\BlockCommentCommand;
use App\Comment\Application\UseCase\Comment\BlockComment\BlockCommentHandler;
use App\Comment\Application\UseCase\Comment\CreateComment\CreateCommentCommand;
use App\Comment\Application\UseCase\Comment\CreateComment\CreateCommentHandler;
use App\Comment\Application\UseCase\Comment\DeleteComment\DeleteCommentCommand;
use App\Comment\Application\UseCase\Comment\DeleteComment\DeleteCommentHandler;
use App\Comment\Application\UseCase\Comment\GetComments\GetCommentsHandler;
use App\Comment\Application\UseCase\Comment\GetComments\GetCommentsQuery;
use App\Comment\Application\UseCase\Comment\UnblockComment\UnblockCommentCommand;
use App\Comment\Application\UseCase\Comment\UnblockComment\UnblockCommentHandler;
use App\Comment\Application\UseCase\Comment\UpdateComment\UpdateCommentCommand;
use App\Comment\Application\UseCase\Comment\UpdateComment\UpdateCommentHandler;
use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Comment\Domain\ValueObject\CommentId;
use App\Comment\Infrastructure\Symfony\Dto\Input\CreateCommentInput;
use App\Comment\Infrastructure\Symfony\Dto\Input\UpdateCommentInput;
use App\Comment\Infrastructure\Symfony\Dto\Output\CommentOutput;
use App\Shared\Domain\Port\EventBusPort;
use App\Shared\Domain\Port\UuidGeneratorPort;
use App\Shared\Domain\ValueObject\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/comment')]
final class CommentController extends AbstractController
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly UserRepositoryInterface    $userRepository,
        private readonly UuidGeneratorPort          $uuidGenerator,
        private readonly EventBusPort               $eventBus,
    )
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route(methods: [Request::METHOD_POST])]
    public function create(
        #[MapRequestPayload] CreateCommentInput $input
    ): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \LogicException();
        }

        $command = new CreateCommentCommand(
            new UserId($user->getId()),
            $input->message
        );

        $handler = new CreateCommentHandler(
            $this->commentRepository,
            $this->uuidGenerator,
            $this->eventBus,
        );

        $response = $handler($command);

        return $this->json(CommentOutput::fromDomain($response->comment()));
    }

    #[Route(methods: [Request::METHOD_GET])]
    public function readPublic(Request $request): JsonResponse
    {
        $query = new GetCommentsQuery(
            new Pagination(
                $request->query->getInt('page', 1),
                $request->query->getInt('max', 20)
            ),
        );

        $handler = new GetCommentsHandler(
            $this->commentRepository
        );

        $result = $handler($query);

        return $this->json(array_map(function (Comment $comment) {
            return CommentOutput::fromDomain($comment);
        }, $result->comments()));
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(methods: [Request::METHOD_GET])]
    public function readAll(Request $request): JsonResponse
    {
        $query = new GetCommentsQuery(
            new Pagination(
                $request->query->getInt('page', 1),
                $request->query->getInt('max', 20)
            ),
            true
        );

        $handler = new GetCommentsHandler(
            $this->commentRepository
        );

        $result = $handler($query);

        return $this->json(array_map(function (Comment $comment) {
            return CommentOutput::fromDomain($comment);
        }, $result->comments()));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', methods: [Request::METHOD_PATCH])]
    public function update(
        #[MapRequestPayload]
        UpdateCommentInput $input,
        string             $id
    ): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \LogicException();
        }

        $command = new UpdateCommentCommand(
            new CommentId($id),
            $input->message,
            new UserId($user->getId()),
        );

        $handler = new UpdateCommentHandler(
            $this->commentRepository,
            $this->userRepository,
            $this->eventBus,
        );

        $result = $handler($command);

        return $this->json(CommentOutput::fromDomain($result->comment()));
    }

    #[Route('/{id}', methods: [Request::METHOD_DELETE])]
    public function delete(string $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \LogicException();
        }

        $command = new DeleteCommentCommand(
            new CommentId($id),
            new UserId($user->getId()),
        );

        $handler = new DeleteCommentHandler(
            $this->commentRepository,
            $this->userRepository,
            $this->eventBus,
        );

        $handler($command);

        return $this->json([
            'result' => true
        ]);
    }

    #[Route('/{id}/block', methods: [Request::METHOD_PATCH])]
    public function block(string $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \LogicException();
        }

        $command = new BlockCommentCommand(
            new CommentId($id),
            new UserId($user->getId()),
        );

        $handler = new BlockCommentHandler(
            $this->commentRepository,
            $this->userRepository,
            $this->eventBus,
        );

        $handler($command);

        return $this->json([
            'result' => true,
        ]);
    }

    #[Route('/{id}/unblock', methods: [Request::METHOD_PATCH])]
    public function unblock(string $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \LogicException();
        }

        $command = new UnblockCommentCommand(
            new CommentId($id),
            new UserId($user->getId()),
        );

        $handler = new UnblockCommentHandler(
            $this->commentRepository,
            $this->userRepository,
            $this->eventBus,
        );

        $handler($command);

        return $this->json(['result' => true]);
    }
}
