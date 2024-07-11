<?php

namespace App\DTO;

class PasswordChangeDTO
{
    public string $oldPassword;
    public string $newPassword;

    public string $repeatPassword;
}
