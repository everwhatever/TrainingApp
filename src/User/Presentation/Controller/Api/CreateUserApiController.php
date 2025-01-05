<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Api;

use App\User\Application\Command\CreateUserCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
            return new JsonResponse(['status' => 400, 'message' => 'Invalid JSON body'], 400);
        }

        if (!$userData['email']) {
            return new JsonResponse(['status' => 400, 'message' => 'Email cannot be empty'], 400);
        }

        if (!$userData['password']) {
            return new JsonResponse(['status' => 400, 'message' => 'Password cannot be empty'], 400);
        }

        $command = new CreateUserCommand(
            $userData['email'],
            $userData['password'],
            $userData['first_name'] ?? '',
            $userData['last_name'] ?? ''
        );

        try {
            $this->messageBus->dispatch($command);

            return new JsonResponse(['status' => 201, 'message' => 'User created'], 201);
        } catch (Throwable $exception) {
            return new JsonResponse(['status' => 500, 'message' => $exception->getMessage()], 500);
        }
    }
}