<?php

namespace Modules\Requestable\Entities;

use Astrotomic\Translatable\Translatable;
use Modules\Core\Icrud\Entities\CrudModel;
use Modules\Iforms\Support\Traits\Formeable;

class Category extends CrudModel
{
  use Translatable, Formeable;
  
  protected $table = 'requestable__categories';
  public $transformer = 'Modules\Requestable\Transformers\CategoryTransformer';
  public $requestValidation = [
    'create' => 'Modules\Requestable\Http\Requests\CreateCategoryRequest',
    'update' => 'Modules\Requestable\Http\Requests\UpdateCategoryRequest',
  ];
  public $translatedAttributes = [
    'title'
  ];
  protected $fillable = [
    'type',
    'time_elapsed_to_cancel',
    'events',
    'eta_event',
    'requestable_type',
    'options',
    'form_id',
  ];
  
  protected $casts = [
    'events' => 'array',
    'options' => 'array',
  ];
  
  public function statuses(){
    return $this->hasMany(Status::class);
  }
  public function defaultStatus(){
    return $this->statuses->where("default",true)->first();
  }
}
