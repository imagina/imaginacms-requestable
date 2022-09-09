<?php

namespace Modules\Requestable\Entities;

use Modules\Core\Icrud\Entities\CrudModel;
use Modules\Ifillable\Traits\isFillable;

class AutomationRule extends CrudModel
{
  
  use isFillable;

  protected $table = 'requestable__automation_rules';
  public $transformer = 'Modules\Requestable\Transformers\AutomationRuleTransformer';
  public $requestValidation = [
      'create' => 'Modules\Requestable\Http\Requests\CreateAutomationRuleRequest',
      'update' => 'Modules\Requestable\Http\Requests\UpdateAutomationRuleRequest',
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
    'name',
    'run_type',
    'run_config',
    'working_hours',
    'status_id'
  ];

  protected $with = [
    "fields"
  ];

  protected $casts = [
    'run_config' => 'array'
  ];


  //============== RELATIONS ==============//

  public function status(){
    return $this->belongsTo(Status::class);
  }

  //==================== ACCESORS ==============//

  public function getRunConfigAttribute($value)
  {
    return json_decode($value);
  }


  //==================== MUTATORS ==============//
  
  public function setRunConfigAttribute($value)
  {
    $this->attributes['run_config'] = json_encode($value);
  }


}
