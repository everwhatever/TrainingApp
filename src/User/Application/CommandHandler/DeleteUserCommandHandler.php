<?php

declare(strict_types=1);

namespace App\User\Application\CommandHandler;

use App\User\Application\Command\DeleteUserCommand;
use App\User\Domain\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class DeleteUserCommandHandler
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(DeleteUserCommand $command): void
    {
        $userId = $command->userId;
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $this->userRepository->delete($userId);
    }
}