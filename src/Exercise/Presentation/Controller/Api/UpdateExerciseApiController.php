<?php

declare(strict_types=1);

namespace App\Exercise\Presentation\Controller\Api;

use App\Exercise\Application\Command\UpdateExerciseCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/exercise/{id}', name: 'api_exercise_update', methods: ['PUT'])]
readonly class UpdateExerciseApiController
{
    public function __construct(private MessageBusInterface $messageBus) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($id) || empty($data['name']) || empty($data['description'])) {
            return new JsonResponse(['message' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
        }

        $command = new UpdateExerciseCommand(
            $id,
            $data['name'],
            $data['description'],
            $data['video_url'] ?? null,
            $data['image_url'] ?? null
        );

        try {
            $this->messageBus->dispatch($command);
            return new JsonResponse(['message' => 'Exercise updated successfully'], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
