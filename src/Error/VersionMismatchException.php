<?php
declare(strict_types=1);

namespace CakeDumpSql\Error;

class VersionMismatchException extends \Exception
{
    protected $message = 'Your pg_dump version is not compatible with the used PostgreSQL server';
}
