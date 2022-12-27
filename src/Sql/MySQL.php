<?php
declare(strict_types=1);

namespace CakeDumpSql\Sql;

use Symfony\Component\Process\Process;

class MySQL extends SqlBase
{
    protected string $command = 'mysqldump';

    /**
     * @return string
     */
    public function dump(): string
    {
        $config = $this->getConfig();

        $command = [
            $this->command,
            '--user="' . ($config['username'] ?? '') . '"',
            '--password="' . ($config['password'] ?? '') . '"',
            '--default-character-set=' . ($config['encoding'] ?? 'utf8'),
            '--host=' . ($config['host'] ?? 'localhost'),
            '--databases ' . $config['database'],
            '--no-create-db',
        ];
        if ($this->isDataOnly()) {
            $command[] = ' --no-create-info';
        }

        $process = new Process($command);
        $process->run();

        if ($process->isSuccessful()) {
            return $process->getOutput();
        } else {
            return $process->getErrorOutput();
        }
    }
}
