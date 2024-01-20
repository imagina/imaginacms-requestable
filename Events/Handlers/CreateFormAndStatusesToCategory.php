<?php

namespace Modules\Requestable\Events\Handlers;

use Modules\Iforms\Events\SyncFormeable;

class CreateFormAndStatusesToCategory
{
  
  private $log = "Requestable: Events|Handler|CreateFormAndStatusesToCategory|";
  private $formService;
  private $statusService;

  public function __construct()
  {
    $this->formService = app("Modules\Requestable\Services\FormService");
    $this->statusService = app("Modules\Requestable\Services\StatusService");
  }

  public function handle($event)
  {

    \Log::info($this->log."INIT");

    $params = $event->params;

    $data = $params['data'];
    $model = $params['model'];

    
    // Process to create and sync Form with category
    if(!isset($data['form_id'])){

        \Log::info($this->log."Create FORM to category: ".$model->title);

        $systemName = $data['type'];
        $form = $this->formService->create($systemName);

        event(new SyncFormeable($model, ["form_id" => $form->id]));
    }
   
    // Process Statuses
    $config['statuses'] = config('asgard.requestable.config.requestable-leads.statuses');
    $this->statusService->createStatuses($config,$model);


    \Log::info($this->log."END");
    
  }


}
