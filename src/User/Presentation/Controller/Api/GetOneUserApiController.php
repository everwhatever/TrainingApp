<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Api;

use App\User\Application\Query\GetOneUserQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('api/v1/user', name: 'api_get_one_user', methods: ['GET'])]
readonly class GetOneUserApiController
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $findByData = $request->query->all();

        if (empty($findByData)) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        $query = new GetOneUserQuery($findByData);

        try {
            $envelope = $this->messageBus->dispatch($query);
            $handledStamp = $envelope->last(HandledStamp::class);

            $userData = $handledStamp->getResult();

            return new JsonResponse(['status' => 'ok', 'data' => $userData], 200);
        } catch (\Exception $exception) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ExceptionInterface $e) {
            return new JsonResponse(['status' => Response::HTTP_BAD_REQUEST, 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

    }
}