<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Api;

use App\User\Application\Command\UpdateUserCommand;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/user', name: 'api_update_user', methods: ['PUT'])]
readonly class UpdateUserApiController
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

        if (!$userData['id']) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Id cannot be empty'], Response::HTTP_BAD_REQUEST);
        }

        $command = new UpdateUserCommand(
            $userData['id'],
            $userData['first_name'] ?? null,
            $userData['last_name'] ?? null
        );

        try {
            $this->messageBus->dispatch($command);

            return new JsonResponse(['status' => Response::HTTP_OK, 'message' => 'User updated'], Response::HTTP_OK);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $exception) {
            return new JsonResponse(['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}