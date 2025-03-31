<?php

namespace App\Exception;


use Throwable;

interface AppExceptionInterface extends Throwable
{
    public function isHandled(): bool;
}
