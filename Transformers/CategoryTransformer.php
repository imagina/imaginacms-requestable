<?php

namespace Modules\Requestable\Transformers;

use Modules\Core\Icrud\Transformers\CrudResource;

class CategoryTransformer extends CrudResource
{
  /**
   * Method to merge values with response
   *
   * @return array
   */
  public function modelAttributes($request)
  {
    $form = $this->forms->first();
    return [
      "form" => $form ?? '',
      "formId" => $form->id ?? ''
    ];
  }
}
