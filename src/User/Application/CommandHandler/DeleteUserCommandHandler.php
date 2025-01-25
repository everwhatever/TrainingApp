<?php

declare(strict_types=1);

namespace App\User\Application\CommandHandler;

use App\User\Application\Command\DeleteUserCommand;
use App\User\Domain\Event\UserDeletedEvent;
use App\User\Domain\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly class DeleteUserCommandHandler
{
    public function __construct(private UserRepository $userRepository, private EventDispatcherInterface $dispatcher)
    {
    }

    public function __invoke(DeleteUserCommand $command): void
    {
        $userId = $command->userId;
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $userId = Uuid::fromString($userId);

        try {
            $this->userRepository->delete($userId);
        } catch (\Exception $exception) {
            throw new \RuntimeException('Failed to delete user: ' . $exception->getMessage());
        }

        $userDeletedEvent = new UserDeletedEvent($userId, new \DateTimeImmutable());
        $this->dispatcher->dispatch($userDeletedEvent);
    }
}