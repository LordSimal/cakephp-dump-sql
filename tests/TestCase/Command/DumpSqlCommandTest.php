<?php
declare(strict_types=1);

namespace LordSimal\CakephpDumpSql\Test;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * CakeDumpSql\Command\DumpSqlCommand Test Case
 *
 * @uses \CakeDumpSql\Command\DumpSqlCommand
 */
class DumpSqlCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }
    /**
     * Test buildOptionParser method
     *
     * @return void
     * @uses \CakeDumpSql\Command\DumpSqlCommand::buildOptionParser()
     */
    public function testBuildOptionParser(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test execute method
     *
     * @return void
     * @uses \CakeDumpSql\Command\DumpSqlCommand::execute()
     */
    public function testExecute(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
