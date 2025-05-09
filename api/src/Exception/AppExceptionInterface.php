<?php

namespace App\Exception;

interface AppExceptionInterface extends \Throwable
{
    public function isHandled(): bool;
}
