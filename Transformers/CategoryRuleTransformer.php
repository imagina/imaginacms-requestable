<?php

namespace Modules\Requestable\Transformers;

use Modules\Core\Icrud\Transformers\CrudResource;

class CategoryRuleTransformer extends CrudResource
{
  /**
   * Method to merge values with response
   *
   * @return array
   */
  public function modelAttributes($request)
  {
    $data = [
      'statusName' => $this->statusName,
      'formFields' => $this->form_fields,
    ];

    //Translate the labels of the formfields
    if (!is_null($this->form_fields)) {
      foreach ($this->form_fields as $name => $field){
        if(isset($data["formFields"]->$name->props->label)){
          $data["formFields"]->$name->props->label = trans($data["formFields"]->$name->props->label);
        }
      }
    }

    //Response
    return $data;
  }
}
