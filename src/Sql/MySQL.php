<?php
declare(strict_types=1);

namespace CakeDumpSql\Sql;

use Cake\Database\Driver\Mysql as MysqlDriver;
use CakeDumpSql\Error\BinaryNotFoundException;
use Symfony\Component\Process\Process;

class MySQL extends SqlBase
{
    protected string $command = 'mysqldump';

    /**
     * @param array<string, mixed> $config The config array from the connection object
     * @param \Cake\Database\Driver\Mysql $driver The current mysql driver instance
     */
    public function __construct(array $config, protected MysqlDriver $driver)
    {
        parent::__construct($config);

        if ($driver->isMariadb()) {
            $this->command = 'mariadb-dump';
        }
    }

    /**
     * @return string
     * @throws \CakeDumpSql\Error\BinaryNotFoundException
     */
    public function dump(): string
    {
        if (!$this->checkBinary($this->command)) {
            throw new BinaryNotFoundException($this->command . ' was not found');
        }

        $command = [
            $this->command,
            '--user="' . ($this->config['username'] ?? '') . '"',
            '--password="' . ($this->config['password'] ?? '') . '"',
            '--default-character-set=' . ($this->config['encoding'] ?? 'utf8mb4'),
            '--host=' . ($this->config['host'] ?? 'localhost'),
            '--port=' . ($this->config['port'] ?? 3306),
            '--databases',
            $this->config['database'],
        ];

        if ($this->driver->isMariadb()) {
            $command[] = '--skip-create-options';
        } else {
            $command[] = '--no-create-db';
        }

        if ($this->isDataOnly()) {
            $command[] = '--no-create-info';
        }

        $process = Process::fromShellCommandline(implode(' ', $command));
        $process->run();

        $output = $process->getOutput();
        $error = $process->getErrorOutput();

        if (!empty($error)) {
            $this->io->warning($error);
        }

        return $output;
    }
}
