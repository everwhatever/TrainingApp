<?php

declare(strict_types=1);

namespace App\User\Application\CommandHandler;

use App\User\Application\Command\UpdateUserCommand;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly class UpdateUserCommandHandler
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $user = $this->userRepository->findOneBy(['id' => $command->id]);

        if (!$user) {
            throw new \InvalidArgumentException('User not found.');
        }

        if (!is_null($command->firstName)) {
            $user->setFirstName($command->firstName);
        }

        if (!is_null($command->lastName)) {
            $user->setLastName($command->lastName);
        }

        $this->userRepository->save($user);
    }
}