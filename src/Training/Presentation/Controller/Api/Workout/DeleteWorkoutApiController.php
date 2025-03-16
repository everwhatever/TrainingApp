<?php

declare(strict_types=1);

namespace App\Training\Presentation\Controller\Api\Workout;

use App\Training\Application\Command\DeleteWorkoutCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/training/workout', name: 'api_training_workout_delete', methods: ['DELETE'])]
readonly class DeleteWorkoutApiController
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $workoutId = $request->query->get('id');

        if (empty($workoutId)) {
            return new JsonResponse(
                ['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Workout ID is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command = new DeleteWorkoutCommand($workoutId);

        try {
            $this->messageBus->dispatch($command);

            return new JsonResponse(
                ['status' => Response::HTTP_OK, 'message' => 'Workout deleted successfully'],
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                ['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Unexpected error: ' . $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
