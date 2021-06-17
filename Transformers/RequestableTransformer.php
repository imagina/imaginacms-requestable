<?php

namespace Modules\Requestable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Iprofile\Transformers\UserTransformer;

class RequestableTransformer extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'requestableType' => $this->requestable_type,
      'requestableId' => $this->requestable_id,
      'type' => $this->type,
      'config' => $this->config,
      'status' => $this->status,
      'fields' => $this->fields,
      'eta' => $this->eta,
      'createdBy' => $this->created_by,
      'reviewedBy' => $this->reviewed_by,
      'createdByUser' => new UserTransformer($this->whenLoaded('createdByUser')),
      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at
    ];
  }
}
