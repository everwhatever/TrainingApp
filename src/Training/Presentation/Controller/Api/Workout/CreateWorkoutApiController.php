<?php

declare(strict_types=1);

namespace App\Training\Presentation\Controller\Api\Workout;

use App\Training\Application\Command\CreateWorkoutCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/training/workout', name: 'api_training_workout_create', methods: ['POST'])]
class CreateWorkoutApiController
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $workoutData = json_decode($request->getContent(), true);

        if (empty($workoutData)) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        if (!$workoutData['userId']) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'User id cannot be empty'], Response::HTTP_BAD_REQUEST);
        }

        $command = new CreateWorkoutCommand(
            $workoutData['userId']
        );

        try {
            $this->messageBus->dispatch($command);

            return new JsonResponse(['status' => Response::HTTP_CREATED, 'message' => 'Workout created'], Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return new JsonResponse(['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}