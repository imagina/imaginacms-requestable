<?php

namespace Modules\Requestable\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;
use Modules\Core\Icrud\Entities\CrudModel;
use Modules\Ifillable\Traits\isFillable;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\Icomments\Traits\Commentable;

class Requestable extends CrudModel
{
  
  use isFillable,MediaRelation,Commentable;
  
  protected $table = 'requestable__requestables';
  
  public $transformer = 'Modules\Requestable\Transformers\RequestableTransformer';
  
  public $requestValidation = [
    'create' => 'Modules\Requestable\Http\Requests\CreateRequestableRequest',
    'update' => 'Modules\Requestable\Http\Requests\UpdateRequestableRequest',
  ];
  
  protected $with = [
    "fields"
  ];
  protected $fillable = [
    "requestable_type",
    "requestable_id",
    "type",
    "eta",
    "status_id",
    "requested_by",
    "category_id",
    "reviewed_by"
  ];

  /*
  protected $casts = [
    'fields' => 'array'
  ];
  */
  
  
  public function createdByUser(){
    return $this->belongsTo(User::class,'created_by');
  }
  
  
  public function requestedBy(){
    return $this->belongsTo(User::class,'requested_by');
  }
  
  public function category(){
    return $this->belongsTo(Category::class);
  }
  
  public function requestable(){
    return $this->morphTo();
  }
  
  public function status(){
    return $this->belongsTo(Status::class);
  }
  
  /**
   * @return mixed
   */
  public function getConfigAttribute()
  {
    $service = app("Modules\Requestable\Repositories\RequestableRepository");
    $requestableConfigs = collect($service->moduleConfigs())->keyBy("type");
  
    return $requestableConfigs[$this->type];
    
  }
  
}
