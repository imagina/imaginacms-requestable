<?php

namespace Modules\Requestable\Entities;

use Astrotomic\Translatable\Translatable;
use Modules\Core\Icrud\Entities\CrudModel;

class StatusHistory extends CrudModel
{
  
  protected $table = 'requestable__statushistories';
  public $transformer = 'Modules\Requestable\Transformers\StatusHistoryTransformer';
  public $requestValidation = [
    'create' => 'Modules\Requestable\Http\Requests\CreateStatusHistoryRequest',
    'update' => 'Modules\Requestable\Http\Requests\UpdateStatusHistoryRequest',
  ];
  
  protected $fillable = [
    'requestable_id',
    'value',
    'comment',
  ];
}
