<?php
declare(strict_types=1);

namespace CakeDumpSql\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\I18n\FrozenTime;
use Cake\TestSuite\TestCase;

class DumpSqlCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    public function testCommand(): void
    {
        $postsTable = $this->fetchTable('Posts');
        $entity = $postsTable->newEmptyEntity();
        $entity = $postsTable->patchEntity($entity, [
            'title' => 'Testtitle',
            'created' => new FrozenTime(),
            'modified' => new FrozenTime(),
        ]);
        $postsTable->save($entity);

        $this->exec('dump_sql');
        $this->assertOutputContains('CREATE TABLE IF NOT EXISTS "posts"');
        $this->assertOutputContains('INSERT INTO posts VALUES(');
        $this->assertExitCode(0);
    }

    public function testCommandDataOnly(): void
    {
        $postsTable = $this->fetchTable('Posts');
        $entity = $postsTable->newEmptyEntity();
        $entity = $postsTable->patchEntity($entity, [
            'title' => 'Testtitle',
            'created' => new FrozenTime(),
            'modified' => new FrozenTime(),
        ]);
        $postsTable->save($entity);

        $this->exec('dump_sql --data-only');
        $this->assertOutputNotContains('CREATE TABLE IF NOT EXISTS "posts"');
        $this->assertOutputContains('INSERT INTO posts VALUES(');
        $this->assertExitCode(0);
    }

    public function testCommandGzipped(): void
    {
        $postsTable = $this->fetchTable('Posts');
        $entity = $postsTable->newEmptyEntity();
        $entity = $postsTable->patchEntity($entity, [
            'title' => 'Testtitle',
            'created' => new FrozenTime(),
            'modified' => new FrozenTime(),
        ]);
        $postsTable->save($entity);

        $this->exec('dump_sql --gzip');
        $result = $this->_out->messages();
        $sql = gzdecode($result[0]);
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS "posts"', $sql);
        $this->assertStringContainsString('INSERT INTO posts VALUES(', $sql);
        $this->assertExitCode(0);
    }
}
