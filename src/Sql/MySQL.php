<?php
declare(strict_types=1);

namespace CakeDumpSql\Sql;

use \Symfony\Component\Process\Process;

class MySQL extends SqlBase
{

  protected string $command = 'mysqldump';

  public function dump(string $host, string $user, string $password, string $db)
  {
    $command = sprintf('%s -h %s -u %s -p%s %s', $this->command, $host, $user, $password, $db);
    if ($this->isDataOnly()) {
      $command .= ' --no-create-info';
    }

    $process = new Process(explode(' ', $command));
    $process->run();

    if ($process->isSuccessful()) {
      return $process->getOutput();
    } else {
      return $process->getErrorOutput();
    }
  }

}