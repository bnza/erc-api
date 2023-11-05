<?php

namespace App\Entity\M2M;

use App\Entity\UserRolesTrait;
use Symfony\Component\Uid\Uuid;

class SitesUsers
{
    use UserRolesTrait;
    private int $id;

    public Uuid $user;

    public int $site;
}
