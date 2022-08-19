<?php

namespace Modules\Requestable\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Requestable\Entities\Status;
use Modules\Requestable\Repositories\StatusRepository;

use Illuminate\Http\Request;

class StatusApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(Status $model, StatusRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }

  /**
  * Update order (Position Request)
  * @param $request
  */
  public function updateOrderStatus(Request $request)
  {
    \DB::beginTransaction();
    try {

      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);

      //Get data
      $data = $request->input('attributes');

      //Service
      $statusService = app("Modules\Requestable\Services\StatusService");
      $statusService->updateOrderPosition($data['category'],$params);
      
      //Response
      $response = ["data" => "Order Updated"];
      
      \DB::commit(); //Commit to Data Base
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    
    }
    //Return response
    return response()->json($response, $status ?? 200);
  }

}
