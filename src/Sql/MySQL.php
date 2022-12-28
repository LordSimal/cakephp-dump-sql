<?php
declare(strict_types=1);

namespace CakeDumpSql\Sql;

use CakeDumpSql\Error\BinaryNotFound;
use Symfony\Component\Process\Process;

class MySQL extends SqlBase
{
    protected string $command = 'mysqldump';

    /**
     * @return string
     * @throws \CakeDumpSql\Error\BinaryNotFound
     */
    public function dump(): string
    {
        if (!$this->checkBinary($this->command)) {
            throw new BinaryNotFound($this->command . ' was not found');
        }

        $config = $this->getConfig();

        $command = [
            $this->command,
            '--user="' . ($config['username'] ?? '') . '"',
            '--password="' . ($config['password'] ?? '') . '"',
            '--default-character-set=' . ($config['encoding'] ?? 'utf8mb4'),
            '--host=' . ($config['host'] ?? 'localhost'),
            '--databases',
            $config['database'],
            '--no-create-db',
        ];
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
