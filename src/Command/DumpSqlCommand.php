<?php
declare(strict_types=1);

namespace CakeDumpSql\Command;

use Cake\Command\Command;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use CakeDumpSql\Error\UnknownDriverException;
use CakeDumpSql\Sql\MySQL as CakeDumpMySQL;
use CakeDumpSql\Sql\PostgreSQL as CakeDumpPostgres;
use CakeDumpSql\Sql\Sqlite as CakeDumpSqlite;

/**
 * DumpSQL command.
 */
class DumpSqlCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addArgument('datasource', [
            'help' => 'The name of the datasource config entry. Defaults to "default"',
        ]);
        $parser->addOption('gzip', [
            'help' => 'Compress the dump using the gzip program which must be in your $PATH.',
        ]);
        $parser->addOption('data-only', [
          'help' => 'Set this option to only export data, no structure',
        ]);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @return int The exit code
     * @throws \CakeDumpSql\Error\BinaryNotFoundException
     * @throws \CakeDumpSql\Error\UnknownDriverException
     * @throws \CakeDumpSql\Error\VersionMismatchException
     */
    public function execute(): int
    {
        $datasource = $this->args->getArgument('datasource') ?? 'default';
        $gzip = $this->args->getOption('gzip') !== null;
        $dataOnly = $this->args->getOption('data-only') !== null;

        try {
            $connection = ConnectionManager::get($datasource);
        } catch (MissingDatasourceConfigException $e) {
            $this->io->err($e->getMessage());

            return self::CODE_ERROR;
        }

        $driver = $connection->getDriver();
        if ($driver instanceof Mysql) {
            $object = new CakeDumpMySQL($connection->config(), $driver);
        } elseif ($driver instanceof Sqlite) {
            $object = new CakeDumpSqlite($connection->config());
        } elseif ($driver instanceof Postgres) {
            $object = new CakeDumpPostgres($connection->config());
        } else {
            $message = sprintf('Unknown driver "%s" given.', get_class($driver));
            throw new UnknownDriverException($message);
        }

        $object->setIo($this->io);
        $object->setDataOnly($dataOnly);
        $result = $object->dump();

        if ($gzip) {
            if (function_exists('gzencode')) {
                $result = gzencode($result, 9);
                if ($result === false) {
                    $this->io->err('Failed to gzip the dump!');

                    return self::CODE_ERROR;
                }
            } else {
                $this->io->err('Your PHP installation does not have zlib support to create a gzip file!');

                return self::CODE_ERROR;
            }
        }

        $this->io->out($result);

        return self::CODE_SUCCESS;
    }
}
