<?php

namespace Modules\Requestable\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

//Events
use Modules\Requestable\Events\RequestableWasCreated;
use Modules\Requestable\Events\RequestableWasUpdated;

//Handlers
use Modules\Requestable\Events\Handlers\CheckStatusRequestable;
use Modules\Iforms\Events\LeadWasCreated;
use Modules\Requestable\Events\Handlers\CreateRequestableByLeadData;


class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        RequestableWasCreated::class => [
            //CheckStatusRequestable::class // Dejar comentado aun en prueba
        ],
        RequestableWasUpdated::class => [
            //CheckStatusRequestable::class // Dejar comentado aun en prueba
        ],
        LeadWasCreated::class => [
            CreateRequestableByLeadData::class
        ],
        
    ];
}
