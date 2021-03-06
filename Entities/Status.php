<?php

namespace Modules\Requestable\Entities;

use Astrotomic\Translatable\Translatable;
use Modules\Core\Icrud\Entities\CrudModel;

class Status extends CrudModel
{
  use Translatable;

  protected $table = 'requestable__statuses';
  public $transformer = 'Modules\Requestable\Transformers\StatusTransformer';
  public $requestValidation = [
    'create' => 'Modules\Requestable\Http\Requests\CreateStatusRequest',
    'update' => 'Modules\Requestable\Http\Requests\UpdateStatusRequest',
  ];
  public $translatedAttributes = [
    'title'
  ];
  protected $fillable = [
    'value',
    'category_id',
    'events',
    'final',
    'default',
    'cancelled_elapsed_time',
    'delete_request'
  ];

  protected $fakeColumns = ['events'];

  protected $casts = [
    'events' => 'array'
  ];

  public function setDefaultAttribute($value){
    if($value){
      Status::where("default",true)->where("category_id",$this->category_id)->update(["default" => false]);
    }
    $this->attributes['default'] = $value;
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }
}
