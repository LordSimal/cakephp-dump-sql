<?php
declare(strict_types=1);

namespace TestApp\Driver;

use Cake\Database\Driver;
use Cake\Database\Driver\SqlDialectTrait;
use Cake\Database\Schema\MysqlSchemaDialect;
use Cake\Database\Schema\SchemaDialect;

class MyDriver extends Driver
{
    use SqlDialectTrait;

    public function connect(): bool
    {
        return true;
    }

    public function enabled(): bool
    {
        return true;
    }

    public function schemaDialect(): SchemaDialect
    {
        return new MysqlSchemaDialect($this);
    }

    public function disableForeignKeySQL(): string
    {
        return "";
    }

    public function enableForeignKeySQL(): string
    {
        return "";
    }

    public function supportsDynamicConstraints(): bool
    {
        return true;
    }
}
