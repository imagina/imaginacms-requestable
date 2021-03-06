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
    if(!empty($this->requestable_type) && !empty($this->requestable_id)){
      $model = $this->requestable_type::find($this->requestable_id);
    }
    return [
      "requestableUrl" => isset($model->url) ? $model->url : "",
      "statusValue" => $this->status->value,
      "files" => MediaTransformer::collection($this->whenLoaded('files'))
    ];
    
  }
}
