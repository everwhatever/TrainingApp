<?php

declare(strict_types=1);

namespace App\User\Application\CommandHandler;

use App\User\Application\Command\CreateUserCommand;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly class CreateUserCommandHandler
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $password = Password::fromString($command->plainPassword);
        $email = new Email($command->email);

        $user = new User(
            Uuid::v4(),
            $email,
            $password,
            $command->firstName,
            $command->lastName,
        );

        $this->userRepository->save($user);
    }
}