<?php

namespace App\Entity\Data\M2M;

use App\Entity\Data\Site;
use App\Entity\Data\User;
use Symfony\Component\Uid\Uuid;

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
