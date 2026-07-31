<?php

namespace App\Providers;

use App\Database\MySqlGrammar;
use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        $this->app['events']->listen(ConnectionEstablished::class, function (ConnectionEstablished $event) {
            $event->connection->setQueryGrammar(new MySqlGrammar($event->connection));
        });
    }
}
