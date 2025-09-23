<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\\Repository\\UserRepository")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $username = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $password = '';

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $email = '';

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = ['ROLE_USER'];

    /**
     * @ORM\Column(name="mfa_secret", type="string", length=255, nullable=true)
     */
    private ?string $mfaSecret = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
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
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_values(array_unique(array_map('strtoupper', $roles)));

        return $this;
    }

    public function addRole(string $role): self
    {
        $this->roles[] = strtoupper($role);
        $this->roles = array_values(array_unique($this->roles));

        return $this;
    }

    public function getMfaSecret(): ?string
    {
        return $this->mfaSecret;
    }

    public function setMfaSecret(?string $mfaSecret): self
    {
        $this->mfaSecret = $mfaSecret;

        return $this;
    }

    public function isMfaEnabled(): bool
    {
        return $this->mfaSecret !== null && $this->mfaSecret !== '';
    }

    public function eraseCredentials(): void
    {
        // No-op: passwords are hashed and MFA secrets are stored at rest.
    }
}
