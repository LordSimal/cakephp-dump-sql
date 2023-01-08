<?php
declare(strict_types=1);

namespace TestApp\Driver;

use Cake\Database\Driver;
use Cake\Database\DriverFeatureEnum;
use Cake\Database\Schema\MysqlSchemaDialect;
use Cake\Database\Schema\SchemaDialect;

class MyDriver extends Driver
{
    public function connect(): void
    {
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

    public function supports(DriverFeatureEnum $feature): bool
    {
        return true;
    }
}
