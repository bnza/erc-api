<?php

namespace App\Entity\Data;

use App\Entity\Data\M2M\SitesUsers;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private Uuid $id;

    public string $email;

    public iterable $sites;

    private string $password;

    public ?string $plainPassword = null;

    private array $roles = ['ROLE_USER'];

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getSitesPrivileges(): array
    {
        if (!$this->sites instanceof PersistentCollection) {
            throw new \InvalidArgumentException(sprintf('%s required', PersistentCollection::class));
        }

        return $this->sites->reduce(function (array $sitesPrivileges, SitesUsers $sitesUser) {
            $sitesPrivileges[$sitesUser->site->getId()] = $sitesUser->privileges;

            return $sitesPrivileges;
        }, []);
    }
}
