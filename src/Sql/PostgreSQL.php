<?php
declare(strict_types=1);

namespace CakeDumpSql\Sql;

use Symfony\Component\Process\Process;

class PostgreSQL extends SqlBase
{
    protected string $command = 'pg_dump';

    /**
     * @return string
     */
    public function dump(): string
    {
        $config = $this->getConfig();
        $passFile = $this->writePassFile();

        $command = [
            'PGPASSFILE=' . $passFile,
            $this->command,
            '--host=' . ($config['host'] ?? 'localhost'),
            '--username=' . ($config['username'] ?? ''),
            '--dbname="' . ($config['database'] ?? '') . '"',
        ];
        if ($this->isDataOnly()) {
            $command[] = '--data-only';
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

    /**
     * Write a .pgpass file containing the login data
     *
     * @return string The absolute path to the file
     */
    private function writePassFile(): string
    {
        $config = $this->getConfig();

        $passwordParts = [
            empty($config['host']) ? 'localhost' : $config['host'],
            empty($config['port']) ? '5432' : $config['port'],
            // Database
            '*',
            $config['username'],
            $config['password'],
        ];

        // Escape colon and backslash characters in entries.
        // @see http://www.postgresql.org/docs/9.1/static/libpq-pgpass.html
        array_walk($passwordParts, function (string &$part) {
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
