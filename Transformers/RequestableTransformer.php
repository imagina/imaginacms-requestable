<?php

namespace Modules\Requestable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Iprofile\Transformers\UserTransformer;
use Modules\Core\Icrud\Transformers\CrudResource;
use Modules\Media\Transformers\NewTransformers\MediaTransformer;

class RequestableTransformer extends CrudResource
{
  /**
   * Method to merge values with response
   *
   * @return array
   */
  public function modelAttributes($request)
  {
    if (!empty($this->requestable_type) && !empty($this->requestable_id)) {
      $model = $this->requestable_type::find($this->requestable_id);
    }
    return [
      "requestableUrl" => isset($model->url) ? $model->url : "",
      "statusValue" => isset($this->status) ? $this->status->value : null,
      "comment" => null,
      'conversation' => $this->whenLoaded('conversation'),
      "fields" => $this->includeLabelFromFormtoFields()//TODO: Fix this with the update of the name in the Iform
    ];
  }

  public function includeLabelFromFormtoFields()
  {
    $fields = [];
    //Get fillable data
    if (in_array('fields', array_keys($this->getRelations())) && method_exists($this->resource, 'formatFillableToModel')) {
      $fields = json_decode(json_encode($this->transformData($this->fields)));
    }
    //Search the related form and map the field to include the label
    $form = isset($this->category->forms) ? $this->category->forms->first() : null;
    if ($form) {
      foreach ($fields as $field) {
        $formField = $form->fields->where('name', $field->name)->first();
        if ($formField) $field->label = $formField->label;
      }
    }
    //Response
    return $fields;
  }
}
