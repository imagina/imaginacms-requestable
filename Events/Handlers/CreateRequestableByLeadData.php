<?php

namespace Modules\Requestable\Events\Handlers;

use Modules\Iforms\Events\LeadWasCreated;

class CreateRequestableByLeadData
{

  public function handle(LeadWasCreated $event)
  {
    $values = $event->entity->values;
    $lead = $event->entity;
    $form = \DB::table('iforms__forms')->where("id", $lead->form_id)->first();
    if (isset($form->parent_id) && !is_null($form->parent_id)) {
      $parentForm = \DB::table('iforms__forms')->where("id", $form->parent_id)->first();
      $id = $parentForm->id;
    } else {
      $id = $event->entity->form_id;
    }
    $formeableData = \DB::table('iforms__formeable')->where("form_id", $id)
      ->where("formeable_type", 'Modules\Requestable\Entities\Category')->first();
    if (isset($formeableData->id)) {
      $values['category_id'] = $formeableData->formeable_id;
      app('Modules\Requestable\Services\RequestableService')->create($values);
    }
  }
}