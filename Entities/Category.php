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
    'default_status',
    'events',
    'eta_event',
    'requestable_type',
    'form_id',
  ];
  
  protected $casts = [
    'events' => 'array'
  ];
  
}
