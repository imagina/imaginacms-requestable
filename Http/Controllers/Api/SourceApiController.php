<?php

namespace Modules\Requestable\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Requestable\Entities\Source;
use Modules\Requestable\Repositories\SourceRepository;

class SourceApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(Source $model, SourceRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }
}
