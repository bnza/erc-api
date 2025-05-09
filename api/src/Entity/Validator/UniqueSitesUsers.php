<?php

namespace App\Entity\Validator;

readonly class UniqueSitesUsers extends AbstractUnique
{
    public int $site;
    public string $user;

    public function __construct(array $criteria, public bool $unique)
    {
        foreach (['site', 'user'] as $prop) {
            if (!isset($criteria[$prop])) {
                throw new \InvalidArgumentException("Missing '$prop' criteria");
            }
            $this->$prop = $criteria[$prop];
        }
    }

    public function getId(): string|int
    {
        return sprintf('%s.%s', $this->site, $this->user);
    }
}
