<?php

declare(strict_types=1);

namespace App\Exercise\Presentation\Controller\Api;

use App\Exercise\Application\Query\GetOneExerciseQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/exercise/{id}', name: 'api_exercise_get_one', methods: ['GET'])]
readonly class GetExerciseApiController
{
    public function __construct(private MessageBusInterface $messageBus) {}

    public function __invoke(string $id): JsonResponse
    {
        if (empty($id)) {
            return new JsonResponse(['message' => 'Exercise ID is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $envelope = $this->messageBus->dispatch(new GetOneExerciseQuery($id));
            /** @var HandledStamp|null $handled */
            $handled = $envelope->last(HandledStamp::class);

            $exercise = $handled?->getResult();

            if (!$exercise) {
                return new JsonResponse(['message' => 'Exercise not found'], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse(['data' => $exercise], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
