<?php
declare(strict_types=1);

namespace CakeDumpSql\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use CakeDumpSql\Error\UnknownDriverException;

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
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int The exit code
     * @throws \CakeDumpSql\Error\UnknownDriverException
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $datasource = $args->getArgument('datasource') ?? 'default';
        $gzip = $args->getOption('gzip') !== null;
        $dataOnly = $args->getOption('data-only') !== null;

        try {
            $connection = ConnectionManager::get($datasource);
        } catch (MissingDatasourceConfigException $e) {
            $io->err($e->getMessage());

            return self::CODE_ERROR;
        }

        $driver = $connection->getDriver();
        switch (get_class($driver)) {
            case Mysql::class:
                $object = new \CakeDumpSql\Sql\MySQL($connection->config());
                break;
            case Sqlite::class:
                $object = new \CakeDumpSql\Sql\Sqlite($connection->config());
                break;
            case Postgres::class:
                $object = new \CakeDumpSql\Sql\PostgreSQL($connection->config());
                break;
            default:
                $message = sprintf('Unknown driver "%s" given.', get_class($driver));
                throw new UnknownDriverException($message);
        }

        $object->setIo($io);
        $object->setDataOnly($dataOnly);
        $result = $object->dump();

        if ($gzip) {
            if (function_exists('gzencode')) {
                $result = gzencode($result, 9);
            } else {
                $io->err('Your PHP installation does not have zlib support to create a gzip file!');

                return self::CODE_ERROR;
            }
        }

        $io->out($result);

        return self::CODE_SUCCESS;
    }
}
