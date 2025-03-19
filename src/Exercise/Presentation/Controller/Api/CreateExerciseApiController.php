<?php

declare(strict_types=1);

namespace App\Exercise\Presentation\Controller\Api;

use App\Exercise\Application\Command\CreateExerciseCommand;
use App\Training\Application\Command\CreateWorkoutCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/exercise', name: 'api_exercise_create', methods: ['POST'])]
readonly class CreateExerciseApiController
{
    public function __construct(private MessageBusInterface $bus) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['description'])) {
            return new JsonResponse(['message' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
        }

        $command = new CreateExerciseCommand($data['name'], $data['description'], $data['video_url'] ?? null, $data['image_url'] ?? null);
        try {
            $this->messageBus->dispatch($command);

            return new JsonResponse(['status' => Response::HTTP_CREATED, 'message' => 'Exercise created'], Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return new JsonResponse(['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
