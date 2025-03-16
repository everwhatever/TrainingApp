<?php

declare(strict_types=1);

namespace App\Training\Presentation\Controller\Api\Workout;

use App\Training\Application\Query\GetAllWorkoutQuery;
use App\Training\Application\Query\GetOneWorkoutQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/training/workout-all', name: 'api_training_workout_get_all', methods: ['GET'])]
readonly class GetAllWorkoutApiController
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        //TODO: add filtering by dates range
        $userId = $request->query->get('userId');

        if (empty($userId)) {
            return new JsonResponse(
                ['status' => Response::HTTP_BAD_REQUEST, 'message' => 'User id is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $query = new GetAllWorkoutQuery($userId);

        try {
            $envelope = $this->messageBus->dispatch($query);
            $handledStamp = $envelope->last(HandledStamp::class);

            if (!$handledStamp) {
                return new JsonResponse(
                    ['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Query was not handled'],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            $workoutData = $handledStamp->getResult();

            if (!$workoutData) {
                return new JsonResponse(
                    ['status' => Response::HTTP_NOT_FOUND, 'message' => 'Workout not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                ['status' => 'ok', 'data' => $workoutData],
                Response::HTTP_OK
            );
        } catch (ExceptionInterface $e) {
            return new JsonResponse(
                ['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Messenger error: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Exception $exception) {
            return new JsonResponse(
                ['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Unexpected error: ' . $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
