<?php

declare(strict_types=1);

namespace App\Training\Presentation\Controller\Api\Series;

use App\Training\Application\Command\CreateSeriesCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/training/series', name: 'api_training_series_create', methods: ['POST'])]
class CreateSeriesApiController
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $seriesData = json_decode($request->getContent(), true);

        if (empty($seriesData) || !isset($seriesData['workoutId'], $seriesData['exerciseName'])) {
            return new JsonResponse(
                ['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Invalid JSON body'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $command = new CreateSeriesCommand(
            $seriesData['workoutId'],
            $seriesData['exerciseName']
        );

        try {
            $this->messageBus->dispatch($command);
            return new JsonResponse(['status' => Response::HTTP_CREATED, 'message' => 'Series created'], Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return new JsonResponse(
                ['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
