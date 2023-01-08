<?php
declare(strict_types=1);

namespace CakeDumpSql\Error;

use Exception;

class VersionMismatchException extends Exception
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected $message = 'Your pg_dump version is not compatible with the used PostgreSQL server';
}
