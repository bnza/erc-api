<?php

namespace App\Entity\M2M;

use Symfony\Component\Uid\Uuid;
use App\Entity\User;
use App\Entity\Site;

class SitesUsers
{
    private Uuid $id;

    public User $user;

    public Site $site;

    public int $privilege;

    public function getId(): Uuid
    {
        return $this->id;
    }
}
