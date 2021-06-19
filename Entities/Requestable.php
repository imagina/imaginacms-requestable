<?php

namespace Modules\Requestable\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;

class Requestable extends Model
{
  
  
  protected $table = 'requestable__requestables';
  
  protected $fillable = [
    "requestable_type",
    "requestable_id",
    "type",
    "eta",
    "status",
    "fields",
    "created_by",
    "reviewed_by"
  ];
  protected $fakeColumns = ['fields'];
  
  protected $casts = [
    'fields' => 'array'
  ];
  
  public function createdByUser()
  {
    return $this->belongsTo(User::class,'created_by');
  }
  
  
  public function requestable()
  {
    return $this->morphTo();
  }
  
  public function getFieldsAttribute($value) {
    
    return json_decode($value);
    
  }
  
  public function setFieldsAttribute($value) {
    $this->attributes['fields'] = json_encode($value);
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
