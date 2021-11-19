<?php

namespace Modules\Requestable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Iprofile\Transformers\UserTransformer;
use Modules\Core\Icrud\Transformers\CrudResource;

class RequestableTransformer extends CrudResource
{
  /**
   * Method to merge values with response
   *
   * @return array
   */
  public function modelAttributes($request)
  {
    $model = $this->requestable_type::find($this->requestable_id);

    return [
      "requestableUrl" => isset($model->url) ? $model->url : "",
    ];
  }
}
