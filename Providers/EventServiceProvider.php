<?php

namespace Modules\Requestable\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

//Events
use Modules\Iforms\Events\FieldIsDeleting;
use Modules\Requestable\Events\Handlers\ValidateFieldIsDeleting;
use Modules\Requestable\Events\RequestableWasCreated;
use Modules\Requestable\Events\RequestableWasUpdated;

use Modules\Requestable\Events\CategoryWasCreated;

//Handlers
use Modules\Requestable\Events\Handlers\CheckStatusRequestable;
use Modules\Iforms\Events\LeadWasCreated;
use Modules\Requestable\Events\Handlers\CreateRequestableByLeadData;
use Modules\Requestable\Events\Handlers\CheckResponsibleRequestable;

use Modules\Requestable\Events\Handlers\CreateFormAndStatusesToCategory;

class EventServiceProvider extends ServiceProvider
{
  protected $listen = [

    RequestableWasCreated::class => [
      CheckStatusRequestable::class
    ],
    RequestableWasUpdated::class => [
      CheckStatusRequestable::class,
      CheckResponsibleRequestable::class
    ],
    LeadWasCreated::class => [
      CreateRequestableByLeadData::class
    ],
    CategoryWasCreated::class => [
      CreateFormAndStatusesToCategory::class
    ],
    FieldIsDeleting::class => [
      ValidateFieldIsDeleting::class,
    ],
  ];
}
