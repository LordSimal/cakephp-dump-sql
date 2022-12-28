<?php
declare(strict_types=1);

namespace TestApp;

use Cake\Console\CommandCollection;
use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\RoutingMiddleware;
use CakeDumpSql\Command\DumpSqlCommand;

class Application extends BaseApplication
{
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue->add(new RoutingMiddleware($this));

        return $middlewareQueue;
    }

    public function console(CommandCollection $commands): CommandCollection
    {
        return $commands->add('dump_sql', DumpSqlCommand::class);
    }
}
