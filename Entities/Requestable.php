<?php

namespace Modules\Requestable\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;
use Modules\Core\Icrud\Entities\CrudModel;

class Requestable extends CrudModel
{
  
  protected $table = 'requestable__requestables';
  public $transformer = 'Modules\Requestable\Transformers\RequestableTransformer';
  public $requestValidation = [
    'create' => 'Modules\Requestable\Http\Requests\CreateRequestableRequest',
    'update' => 'Modules\Requestable\Http\Requests\UpdateRequestableRequest',
  ];
  protected $fillable = [
    "requestable_type",
    "requestable_id",
    "type",
    "eta",
    "status_id",
    "category_id",
    "reviewed_by"
  ];

  protected $casts = [
    'fields' => 'array'
  ];
  
  public function createdByUser()
  {
    return $this->belongsTo(User::class,'created_by');
  }
  
  public function fields()
  {
    return $this->hasMany(Field::class);
  }
  
  public function category()
  {
    return $this->belongsTo(Category::class);
  }
  
  public function requestable()
  {
    return $this->morphTo();
  }
  
  public function getFieldsAttribute($value) {
    
    return json_decode($value);
    
  }
  
  public function status() {
    return $this->belongsTo(Status::class);
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
