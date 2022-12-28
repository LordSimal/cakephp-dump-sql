<?php
declare(strict_types=1);

namespace CakeDumpSql\Sql;

use Cake\Console\ConsoleIo;

abstract class SqlBase
{
    /**
     * @var string The command which needs to be executed to dump the database
     */
    protected string $command;

    /**
     * @var array The config array from the connection object
     */
    protected array $config;

    /**
     * @var bool Indicated if only data should be exported or not
     */
    protected bool $dataOnly = false;

    /**
     * @var \Cake\Console\ConsoleIo The IO instance from the command
     */
    protected ConsoleIo $io;

    /**
     * @param array $config The config array from the connection object
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
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
     * @param \Cake\Console\ConsoleIo $io The IO instance from the command
     * @return void
     */
    public function setIo(ConsoleIo $io): void
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
        $windows = strpos(PHP_OS, 'WIN') === 0;
        $test = $windows ? 'where' : 'command -v';

        return is_executable(trim(shell_exec("$test $command")));
    }
}
