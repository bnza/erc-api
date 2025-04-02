<?php

namespace App\Dto;

class PasswordChangeDto
{
    public string $oldPassword;
    public string $newPassword;

    public string $repeatPassword;
}
