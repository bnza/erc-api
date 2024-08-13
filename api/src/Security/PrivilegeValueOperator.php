<?php

namespace App\Security;

class PrivilegeValueOperator
{
    public function hasPrivilege(int $privileges, ApiPrivileges $privilege): bool
    {
        return (bool) $privileges & $privilege->value;
    }

    public function grantPrivilege(int $privileges, ApiPrivileges $privilege): int
    {
        return $privileges | $privilege->value;
    }

    public function revokePrivilege(int $privileges, ApiPrivileges $privilege): int
    {
        return $privileges & ~$privilege->value;
    }
}
