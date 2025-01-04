<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Entity;

use App\User\Domain\Model\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class DoctrineUser
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
        return new User($this->id, $this->email, $this->password, $this->firstName, $this->lastName);
    }

    public static function fromDomain(User $user): self
    {
        $entity = new self();
        $entity->id = $user->getId();
        $entity->email = $user->getEmail();
        $entity->password = $user->getPassword();
        $entity->firstName = $user->getFirstName();
        $entity->lastName = $user->getLastName();

        return $entity;
    }
}
