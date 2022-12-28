<?php
declare(strict_types=1);

namespace CakeDumpSql\Sql;

use Symfony\Component\Process\Process;

class Sqlite extends SqlBase
{
    protected string $command = 'sqlite3';

    /**
     * @return string
     */
    public function dump(): string
    {
        $dump = $this->getDump();

        if ($this->isDataOnly()) {
            $schema = $this->getSchema();
            file_put_contents(TMP . 'schema.sql', $schema);
            file_put_contents(TMP . 'dump.sql', $dump);

            $dataCommands = [
                'grep',
                '-vx',
                '-f',
                TMP . 'schema.sql',
                TMP . 'dump.sql',
            ];
            $process = new Process($dataCommands);
            $process->run();

            if ($process->isSuccessful()) {
                return $process->getOutput();
            } else {
                return $process->getErrorOutput();
            }
        }

        return $dump;
    }

    /**
     * Get only the schema
     *
     * @return string
     */
    private function getSchema(): string
    {
        $config = $this->getConfig();

        $schemaCommand = [
            $this->command,
            $config['database'],
            '.schema',
        ];

        $process = new Process($schemaCommand);
        $process->run();

        if ($process->isSuccessful()) {
            return $process->getOutput();
        } else {
            return $process->getErrorOutput();
        }
    }

    /**
     * Get the total dump including schema and data
     *
     * @return string
     */
    private function getDump(): string
    {
        $config = $this->getConfig();

        $schemaCommand = [
            $this->command,
            $config['database'],
            '.dump',
        ];

        $process = new Process($schemaCommand);
        $process->run();

        if ($process->isSuccessful()) {
            return $process->getOutput();
        } else {
            return $process->getErrorOutput();
        }
    }
}
