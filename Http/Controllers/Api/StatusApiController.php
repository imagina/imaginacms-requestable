<?php

namespace Modules\Requestable\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Requestable\Entities\Status;
use Modules\Requestable\Repositories\StatusRepository;

class StatusApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(Status $model, StatusRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }
}
