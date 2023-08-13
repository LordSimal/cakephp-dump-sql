<?php
declare(strict_types=1);

namespace CakeDumpSql\Sql;

use CakeDumpSql\Error\BinaryNotFoundException;
use Symfony\Component\Process\Process;

class MySQL extends SqlBase
{
    protected string $command = 'mysqldump';

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
            '--databases',
            $this->config['database'],
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
