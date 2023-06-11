<?php
declare(strict_types=1);

namespace CakeDumpSql\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\DateTime;
use Cake\TestSuite\TestCase;
use CakeDumpSql\Error\UnknownDriverException;
use TestApp\Driver\MyDriver;

class DumpSqlCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        // Sets the TestApp namespace to be used instead of App
        $this->setAppNamespace();
        $this->configApplication(
            'TestApp\Application',
            [PLUGIN_TESTS . 'test_app' . DS . 'config']
        );
    }

    public function testCommand(): void
    {
        $postsTable = $this->fetchTable('Posts');
        $entity = $postsTable->newEmptyEntity();
        $entity = $postsTable->patchEntity($entity, [
            'title' => 'Testtitle',
            'created' => new DateTime(),
            'modified' => new DateTime(),
        ]);
        $postsTable->save($entity);

        $this->exec('dump_sql');

        if ($this->isDBType(Sqlite::class)) {
            $this->assertOutputContains('CREATE TABLE IF NOT EXISTS "posts"');
            $this->assertOutputContains('INSERT INTO posts VALUES(');
        } elseif ($this->isDBType(Mysql::class)) {
            $this->assertOutputContains('CREATE TABLE `posts` (');
            $this->assertOutputContains('INSERT INTO `posts` VALUES (');
        } elseif ($this->isDBType(Postgres::class)) {
            $this->assertOutputContains('CREATE TABLE public.posts');
            $this->assertOutputContains('COPY public.posts (id, title, created, modified) FROM stdin;');
        }
        $this->assertExitCode(0);
    }

    public function testCommandDataOnly(): void
    {
        $postsTable = $this->fetchTable('Posts');
        $entity = $postsTable->newEmptyEntity();
        $entity = $postsTable->patchEntity($entity, [
            'title' => 'Testtitle',
            'created' => new DateTime(),
            'modified' => new DateTime(),
        ]);
        $postsTable->save($entity);

        $this->exec('dump_sql --data-only');
        if ($this->isDBType(Sqlite::class)) {
            $this->assertOutputNotContains('CREATE TABLE IF NOT EXISTS "posts"');
            $this->assertOutputContains('INSERT INTO posts VALUES(');
        } elseif ($this->isDBType(Mysql::class)) {
            $this->assertOutputNotContains('CREATE TABLE `posts` (');
            $this->assertOutputContains('INSERT INTO `posts` VALUES (');
        } elseif ($this->isDBType(Postgres::class)) {
            $this->assertOutputNotContains('CREATE TABLE public.posts');
            $this->assertOutputContains('COPY public.posts (id, title, created, modified) FROM stdin;');
        }
        $this->assertExitCode(0);
    }

    public function testCommandGzipped(): void
    {
        $postsTable = $this->fetchTable('Posts');
        $entity = $postsTable->newEmptyEntity();
        $entity = $postsTable->patchEntity($entity, [
            'title' => 'Testtitle',
            'created' => new DateTime(),
            'modified' => new DateTime(),
        ]);
        $postsTable->save($entity);

        $this->exec('dump_sql --gzip');
        $result = $this->_out->messages();
        $sql = gzdecode($result[0]);
        if ($this->isDBType(Sqlite::class)) {
            $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS "posts"', $sql);
            $this->assertStringContainsString('INSERT INTO posts VALUES(', $sql);
        } elseif ($this->isDBType(Mysql::class)) {
            $this->assertStringContainsString('CREATE TABLE `posts` (', $sql);
            $this->assertStringContainsString('INSERT INTO `posts` VALUES (', $sql);
        } elseif ($this->isDBType(Postgres::class)) {
            $this->assertStringContainsString('CREATE TABLE public.posts', $sql);
            $this->assertStringContainsString('COPY public.posts (id, title, created, modified) FROM stdin;', $sql);
        }
        $this->assertExitCode(0);
    }

    public function testUnknownConnectionName(): void
    {
        $this->exec('dump_sql unknown');
        $this->assertErrorContains('The datasource configuration `unknown` was not found.');
        $this->assertExitCode(1);
    }

    public function testUnknownDriver(): void
    {
        $this->expectException(UnknownDriverException::class);
        $this->expectExceptionMessage('Unknown driver "TestApp\Driver\MyDriver" given.');
        ConnectionManager::setConfig('unknown', [
            'className' => Connection::class,
            'driver' => MyDriver::class,
            'persistent' => false,
            'timezone' => 'UTC',
            'flags' => [],
            'cacheMetadata' => false,
            'log' => false,
            'quoteIdentifiers' => false,
        ]);
        $this->exec('dump_sql unknown');
    }

    private function isDBType(string $type, string $connection = 'default'): bool
    {
        $connection = ConnectionManager::get($connection);
        $driver = $connection->getDriver();

        return get_class($driver) === $type;
    }
}
