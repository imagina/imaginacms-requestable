<?php

namespace Modules\Requestable\Events\Handlers;

use Illuminate\Support\Str;
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
    if (isset($field->system_type) && !is_null($field->system_type) &&
      Str::startsWith($field->system_type,"requestable-internalHidden")) {
      throw new \Exception(trans('requestable::common.errors.fieldProtect'), 409);
    }
  }
}
