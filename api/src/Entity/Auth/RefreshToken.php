<?php

namespace App\Entity\Auth;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens', schema: 'auth')]
class RefreshToken extends BaseRefreshToken
{
}
