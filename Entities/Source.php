<?php

namespace Modules\Requestable\Entities;

use Modules\Core\Icrud\Entities\CrudModel;
use Modules\User\Entities\Sentinel\User;

//traits
use Modules\Media\Support\Traits\MediaRelation;
use Kalnoy\Nestedset\NodeTrait;

class Source extends CrudModel
{
  use MediaRelation,NodeTrait;

  protected $table = 'requestable__sources';
  public $transformer = 'Modules\Requestable\Transformers\SourceTransformer';
  public $repository = 'Modules\Requestable\Repositories\SourceRepository';
  public $requestValidation = [
      'create' => 'Modules\Requestable\Http\Requests\CreateSourceRequest',
      'update' => 'Modules\Requestable\Http\Requests\UpdateSourceRequest',
    ];
  //Instance external/internal events to dispatch with extraData
  public $dispatchesEventsWithBindings = [
    //eg. ['path' => 'path/module/event', 'extraData' => [/*...optional*/]]
    'created' => [],
    'creating' => [],
    'updated' => [],
    'updating' => [],
    'deleting' => [],
    'deleted' => []
  ];
  
  protected $fillable = [
    'title',
    'description',
    'status',
    'parent_id'
  ];

  public $modelRelations = [
    'users' => 'belongsToMany',
  ];

  //============== RELATIONS ==============//

  public function users()
  {
    return $this->belongsToMany(User::class, 'requestable__user_source');
  }

  public function Requestables()
  {
    return $this->hasMany(Requestable::class);
  }

  public function parent()
  {
    return $this->belongsTo(Source::class, 'parent_id');
  }

  public function children()
  {
    return $this->hasMany(Source::class, 'parent_id');
  }

  //==================== ACCESORS ==============//
  
  public function getLftName()
  {
    return 'lft';
  }

  public function getRgtName()
  {
    return 'rgt';
  }

  public function getDepthName()
  {
    return 'depth';
  }

}
