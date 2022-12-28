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
     */
    public function setDataOnly(bool $dataOnly): void
    {
        $this->dataOnly = $dataOnly;
    }

    /**
     * @return \Cake\Console\ConsoleIo
     */
    public function getIo(): ConsoleIo
    {
        return $this->io;
    }

    /**
     * @param \Cake\Console\ConsoleIo $io
     */
    public function setIo(ConsoleIo $io): void
    {
        $this->io = $io;
    }
}
