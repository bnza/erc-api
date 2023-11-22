<?php

namespace App\Entity;

use Doctrine\ORM\PersistentCollection;
use http\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

use App\Entity\M2M\SitesUsers;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use UserRolesTrait;
    private Uuid $id;

    public string $email;

    public iterable $sites;

    private string $password;

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

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getSitesPrivileges(): array {
        if (!$this->sites instanceof PersistentCollection) {
           throw new InvalidArgumentException(sprintf('%s required', PersistentCollection::class));
        }
        return $this->sites->reduce(function (array $sitesPrivileges,SitesUsers $sitesUser) {
            $sitesPrivileges[$sitesUser->site->getId()] = $sitesUser->privilege;
            return $sitesPrivileges;
        }, []);
    }
}
