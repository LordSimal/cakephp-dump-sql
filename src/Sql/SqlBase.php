<?php
declare(strict_types=1);

namespace CakeDumpSql\Sql;

use Cake\Console\ConsoleIoInterface;

abstract class SqlBase
{
    /**
     * @var string The command which needs to be executed to dump the database
     */
    protected string $command;

    /**
     * @var bool Indicated if only data should be exported or not
     */
    protected bool $dataOnly = false;

    /**
     * @var \Cake\Console\ConsoleIoInterface The IO instance from the command
     */
    protected ConsoleIoInterface $io;

    /**
     * @param array $config The config array from the connection object
     */
    public function __construct(public readonly array $config)
    {
    }

    /**
     * @return bool
     */
    public function isDataOnly(): bool
    {
        return $this->dataOnly;
    }

    /**
     * @param bool $dataOnly True if only data should be exported
     * @return void
     */
    public function setDataOnly(bool $dataOnly): void
    {
        $this->dataOnly = $dataOnly;
    }

    /**
     * @param \Cake\Console\ConsoleIoInterface $io The IO instance from the command
     * @return void
     */
    public function setIo(ConsoleIoInterface $io): void
    {
        $this->io = $io;
    }

    /**
     * Check if a given binary is executable or not
     *
     * @param string $command The command to check
     * @return bool
     */
    protected function checkBinary(string $command): bool
    {
        $windows = str_starts_with(PHP_OS, 'WIN');
        $test = $windows ? 'where' : 'command -v';
        $commandResult = shell_exec("$test $command");
        if ($commandResult === null || $commandResult === false) {
            return false;
        }

        return is_executable(trim($commandResult));
    }
}
