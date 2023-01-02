<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('email', message: 'validator.email.unique')]
#[UniqueEntity('username', message: 'validator.username.unique')]
#[ORM\Table('user')]
#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[Assert\Length(
        min : 2,
        max : 50,
        minMessage : "validator.username.min",
        maxMessage : "validator.username.max"
    )]
    #[Assert\NotBlank(message:'validator.notblank')]
    #[ORM\Column(type: 'string', length: 25, unique: true)]
    private $username;

    #[Assert\Regex(
        "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#-+!*$@%_])([#-+!*$@%_\w]{8,100})$/",
        message: "validator.password.regex"
    )]
    #[Assert\NotBlank(message: 'validator.notblank')]
    #[Assert\NotCompromisedPassword]

    #[ORM\Column(type: 'string', length: 64)]
    private $password;

    #[Assert\NotBlank(message: 'validator.notblank')]
    #[Assert\Email(message: "validator.email.format")]
    #[ORM\Column(type: 'string', length: 60, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Task::class)]
    private Collection $tasks;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->uuid = Uuid::v6();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @codeCoverageIgnore
     */
    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setUser($this);
        }

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
