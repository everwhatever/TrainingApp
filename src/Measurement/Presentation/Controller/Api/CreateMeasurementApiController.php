<?php

declare(strict_types=1);

namespace App\Measurement\Presentation\Controller\Api;

use App\Measurement\Application\Command\CreateMeasurementCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/api/v1/measurement', name: 'api_measurement_create', methods: ['POST'])]
readonly class CreateMeasurementApiController
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $measurementData = json_decode($request->getContent(), true);

        if (empty($measurementData)) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        $command = new CreateMeasurementCommand(
            $measurementData['email'],
            $measurementData['password'],
            $measurementData['first_name'] ?? '',
            $measurementData['last_name'] ?? ''
        );

        try {
            $this->messageBus->dispatch($command);

            return new JsonResponse(['status' => Response::HTTP_CREATED, 'message' => 'Measurement created'], Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return new JsonResponse(['status' => Response::HTTP_INTERNAL_SERVER_ERROR, 'message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}