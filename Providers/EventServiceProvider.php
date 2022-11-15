<?php

namespace Modules\Requestable\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

//Events
use Modules\Requestable\Events\RequestableWasCreated;
use Modules\Requestable\Events\RequestableWasUpdated;

//Handlers
use Modules\Requestable\Events\Handlers\CheckStatusRequestable;
use Modules\Iforms\Events\LeadWasCreated;
//use Modules\Requestable\Events\Handlers\CreateRequestableByLeadData;


class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        RequestableWasCreated::class => [
            //heckStatusRequestable::class // Dejar comentador aun en prueba
        ],
        RequestableWasUpdated::class => [
            //CheckStatusRequestable::class // Dejar comentador aun en prueba
        ],
        LeadWasCreated::class => [
            CreateRequestableByLeadData::class
        ],
        
    ];
}
