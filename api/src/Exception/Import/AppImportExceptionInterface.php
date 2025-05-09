<?php

namespace App\Exception\Import;

use App\Exception\AppExceptionInterface;
use Bnza\JobManagerBundle\Exception\ExceptionValuesInterface;

interface AppImportExceptionInterface extends AppExceptionInterface, ExceptionValuesInterface
{
}
