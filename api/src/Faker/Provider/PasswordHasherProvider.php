<?php

namespace App\Faker\Provider;

use App\Entity\User;
use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordHasherProvider extends BaseProvider
{
    public function __construct(Generator $generator, private UserPasswordHasherInterface $hasher)
    {
        parent::__construct($generator);
    }

    public function hashPassword(string $plainPassword)
    {
        return $this->hasher->hashPassword(new User(), $plainPassword);
    }
}
