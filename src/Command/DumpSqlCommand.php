<?php
declare(strict_types=1);

namespace CakeDumpSql\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Driver\Mysql;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;

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

        $result = "";
        $config = $connection->config();
        $driver = $connection->getDriver();
        switch(get_class($driver)) {
          case Mysql::class:
            $object = new \CakeDumpSql\Sql\MySQL();
            $object->setDataOnly($dataOnly);
            $result = $object->dump($config['host'], $config['username'], $config['password'], $config['database']);
            break;
        }

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
