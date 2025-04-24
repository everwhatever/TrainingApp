<?php

declare(strict_types=1);

namespace App\Exercise\Presentation\Controller\Api;

use App\Exercise\Application\Query\GetAllExercisesQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/exercise', name: 'api_exercise_get_all', methods: ['GET'])]
readonly class GetAllExercisesApiController
{
    public function __construct(private MessageBusInterface $messageBus) {}

    public function __invoke(): JsonResponse
    {
        try {
            $envelope = $this->messageBus->dispatch(new GetAllExercisesQuery());
            /** @var HandledStamp|null $handled */
            $handled = $envelope->last(HandledStamp::class);

            $exercises = $handled?->getResult() ?? [];

            return new JsonResponse(['data' => $exercises], Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
