<?php

namespace Modules\Requestable\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

//Events
use Modules\Requestable\Events\RequestableWasUpdated;

//Handlers
use Modules\Requestable\Events\Handlers\CheckStatusRequestable;


class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        /*
        RequestableWasUpdated::class => [
            CheckStatusRequestable::class
        ],
        */
       
    ];
}
