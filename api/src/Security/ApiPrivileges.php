<?php

namespace App\Security;

enum ApiPrivileges: int
{
    case Editor = 0b1;
}
