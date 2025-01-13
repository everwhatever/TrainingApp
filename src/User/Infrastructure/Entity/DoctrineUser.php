<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Entity;

use App\User\Domain\Model\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class DoctrineUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private Uuid $id;

    #[ORM\Column(type: "string", unique: true)]
    private string $email;

    #[ORM\Column(type: "string")]
    private string $password;

    #[ORM\Column(type: "string")]
    private string $firstName;

    #[ORM\Column(type: "string")]
    private string $lastName;

    public function toDomain(): User
    {
        return new User($this->id, new Email($this->email), new Password($this->password), $this->firstName, $this->lastName);
    }

    public static function fromDomain(User $user): self
    {
        $entity = new self();
        $entity->id = $user->getId();
        $entity->email = $user->getEmail()->getEmail();
        $entity->password = $user->getPassword()->getHashedPassword();
        $entity->firstName = $user->getFirstName();
        $entity->lastName = $user->getLastName();

        return $entity;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
