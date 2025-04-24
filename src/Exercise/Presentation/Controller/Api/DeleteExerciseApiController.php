<?php

declare(strict_types=1);

namespace App\Exercise\Presentation\Controller\Api;

use App\Exercise\Application\Command\DeleteExerciseCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/exercise/{id}', name: 'api_exercise_delete', methods: ['DELETE'])]
readonly class DeleteExerciseApiController
{
    public function __construct(private MessageBusInterface $messageBus) {}

    public function __invoke(string $id): JsonResponse
    {
        if (empty($id)) {
            return new JsonResponse(['message' => 'Exercise ID is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->messageBus->dispatch(new DeleteExerciseCommand($id));
            return new JsonResponse(['status' => Response::HTTP_OK, 'message' => 'Exercise deleted'], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
