<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Api;

use App\User\Application\Command\CreateUserCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/api/v1/user', name: 'api_user_create', methods: ['POST'])]
readonly class CreateUserApiController
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $userData = json_decode($request->getContent(), true);

        if (empty($userData)) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        if (!$userData['email']) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Email cannot be empty'], Response::HTTP_BAD_REQUEST);
        }

        if (!$userData['password']) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Password cannot be empty'], Response::HTTP_BAD_REQUEST);
        }

        $command = new CreateUserCommand(
            $userData['email'],
            $userData['password'],
            $userData['first_name'] ?? '',
            $userData['last_name'] ?? ''
        );

        try {
            $this->messageBus->dispatch($command);

            return new JsonResponse(['status' => Response::HTTP_CREATED, 'message' => 'User created'], Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return new JsonResponse(['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}