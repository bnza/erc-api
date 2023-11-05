<?php

namespace App\Entity;

trait UserRolesTrait
{
    private array $roles = ['ROLE_USER'];

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if (in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_EDITOR';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        asort($roles);
        $this->roles = $roles;

        return $this;
    }
}
