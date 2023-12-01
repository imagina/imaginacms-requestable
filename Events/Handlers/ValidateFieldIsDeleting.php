<?php

namespace Modules\Requestable\Events\Handlers;

use Modules\Iforms\Events\FieldIsDeleting;

class ValidateFieldIsDeleting
{

  public function __construct()
  {
  }

  public function handle(FieldIsDeleting $event)
  {
    $entity = $event->getEntity();
    $field = \DB::table('iforms__fields')->where('id', $entity->id)->first();
    if (isset($field->system_type) && is_null($field->system_type)) {
      return false;
    } else {
      return true;
    }
  }
}
