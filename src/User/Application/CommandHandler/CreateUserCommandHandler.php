<?php

declare(strict_types=1);

namespace App\User\Application\CommandHandler;

use App\User\Application\Command\CreateUserCommand;
use App\User\Domain\Event\UserCreatedEvent;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly class CreateUserCommandHandler
{
    public function __construct(private UserRepository $userRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $password = Password::fromString($command->plainPassword);
        $email = new Email($command->email);

        $user = $this->userRepository->findOneBy(['email' => $email->getEmail()]);

        if ($user) {
            throw new \InvalidArgumentException('User already exists.');
        }

        $user = new User(
            Uuid::v4(),
            $email,
            $password,
            $command->firstName,
            $command->lastName,
        );

        try {
            $this->userRepository->save($user);
        } catch (\Exception $exception) {
            throw new \RuntimeException('Failed to create user: ' . $exception->getMessage());
        }

        $userCreatedEvent = new UserCreatedEvent($email, new \DateTimeImmutable());
        $this->eventDispatcher->dispatch($userCreatedEvent);
    }
}