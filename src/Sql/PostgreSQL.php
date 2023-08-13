<?php
declare(strict_types=1);

namespace CakeDumpSql\Sql;

use CakeDumpSql\Error\BinaryNotFoundException;
use CakeDumpSql\Error\VersionMismatchException;
use Symfony\Component\Process\Process;

class PostgreSQL extends SqlBase
{
    protected string $command = 'pg_dump';

    /**
     * @return string
     * @throws \CakeDumpSql\Error\BinaryNotFoundException
     * @throws \CakeDumpSql\Error\VersionMismatchException
     */
    public function dump(): string
    {
        if (!$this->checkBinary($this->command)) {
            throw new BinaryNotFoundException($this->command . ' was not found');
        }

        $passFile = $this->writePassFile();

        $command = [
            'PGPASSFILE=' . $passFile,
            $this->command,
            '--host=' . ($this->config['host'] ?? 'localhost'),
            '--username=' . ($this->config['username'] ?? ''),
            '--dbname="' . ($this->config['database'] ?? '') . '"',
        ];
        if ($this->isDataOnly()) {
            $command[] = '--data-only';
        }

        $process = Process::fromShellCommandline(implode(' ', $command));
        $process->run();

        $output = $process->getOutput();
        $error = $process->getErrorOutput();

        if (str_contains($error, 'server version mismatch')) {
            throw new VersionMismatchException();
        }

        if (!empty($error)) {
            $this->io->warning($error);
        }

        return $output;
    }

    /**
     * Write a .pgpass file containing the login data
     *
     * @return string The absolute path to the file
     */
    private function writePassFile(): string
    {
        $passwordParts = [
            empty($this->config['host']) ? 'localhost' : $this->config['host'],
            empty($this->config['port']) ? '5432' : $this->config['port'],
            // Database
            '*',
            $this->config['username'],
            $this->config['password'],
        ];

        // Escape colon and backslash characters in entries.
        // @see http://www.postgresql.org/docs/9.1/static/libpq-pgpass.html
        array_walk($passwordParts, function (string &$part): void {
            // The order of the replacements is important so that backslashes are
            // not replaced twice.
            $part = str_replace(['\\', ':'], ['\\\\', '\:'], $part);
        });
        $pgpassContents = implode(':', $passwordParts);
        $pgPassPath = TMP . 'psql.pgpass';
        file_put_contents($pgPassPath, $pgpassContents);
        chmod($pgPassPath, 0600);

        return $pgPassPath;
    }
}
