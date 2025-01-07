<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Api;

use App\User\Application\Command\DeleteUserCommand;
use App\User\Domain\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/user', name: 'api_user_delete', methods: ['DELETE'])]
readonly class DeleteUserApiController
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $userId = $request->query->get('id');

        if (!$userId) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $uuid = Uuid::fromString($userId);
            $message = new DeleteUserCommand($uuid);
            $this->messageBus->dispatch($message);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse(['status' => Response::HTTP_NOT_FOUND, 'message' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse(['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}