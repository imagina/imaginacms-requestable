<?php

namespace Modules\Requestable\Http\Controllers\Api;

use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Illuminate\Contracts\Foundation\Application;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Requestable\Http\Requests\CreateRequestableRequest;
use Modules\Requestable\Http\Requests\UpdateRequestableRequest;
use Modules\Requestable\Repositories\RequestableRepository;
use Modules\Requestable\Transformers\RequestableTransformer;


class RequestableApiController extends BaseApiController
{
  private $service;
  
  public function __construct(RequestableRepository $service)
  {
    parent::__construct();
    $this->service = $service;
    
  }
  
  /**
   * GET ITEMS
   *
   * @return mixed
   */
  public function index(Request $request)
  {
    try {
      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);
      
      //Request to Repository
      $newRequest = $this->service->getItemsBy($params);
      
      //Response
      $response = [
        "data" => RequestableTransformer::collection($newRequest)
      ];
      
      //If request pagination add meta-page
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($newRequest)] : false;
    } catch (\Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    
    //Return response
    return response()->json($response, $status ?? 200);
  }
  
  
  /**
   * GET ITEMS
   *
   * @return mixed
   */
  public function config(Request $request)
  {
    try {
      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);
      
      //Request to Repository
      //$newRequest = $this->service->getItemsBy($params);
      
      //Response
      $response = [
        "data" => config('asgard.requestable.config.requests')
      ];
      
      //If request pagination add meta-page
      //$params->page ? $response["meta"] = ["page" => $this->pageTransformer($newRequest)] : false;
    } catch (\Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    
    //Return response
    return response()->json($response, $status ?? 200);
  }
  
  
  /**
   * GET A ITEM
   *
   * @param $criteria
   * @return mixed
   */
  public function show($criteria, Request $request)
  {
    try {
      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);
      
      //Request to Repository
      $newRequest = $this->service->getItem($criteria, $params);

      //Break if no found item
      if (!$newRequest) throw new \Exception('Item not found', 404);
      
      //Response
      $response = ["data" => new RequestableTransformer($newRequest)];
      
    } catch (\Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    
    //Return response
    return response()->json($response, $status ?? 200);
  }
  
  
  /**
   * CREATE A ITEM
   *
   * @param Request $request
   * @return mixed
   */
  public function create(Request $request)
  {
    \DB::beginTransaction();
    try {
      //Get data
      $data = $request->input('attributes');

      $params = $this->getParamsRequest($request);
      //Validate Request
      $this->validateRequestApi(new CreateRequestableRequest((array)$data));
  
      
      $requestableConfigs = collect($this->service->moduleConfigs())->keyBy("type");
  
      $requestConfig = $requestableConfigs[$data["type"]];
      $defaultStatus = $requestConfig["defaultStatus"] ?? 0; // 0 = pending
      $eventPath = $requestConfig["events"]["create"] ?? null;

      $data["status"] = $defaultStatus;
      $data["created_by"] = $params->user->id;
      
      //Create item
      $model = $this->service->create($data);
   
      if ($model && $eventPath)
        event(new $eventPath($model, $requestConfig, $params->user));
      
      //Response
      $response = ["data" => new RequestableTransformer($model)];
      
      \DB::commit(); //Commit to Data Base
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    //Return response
    return response()->json($response, $status ?? 200);
  }
  
  
  /**
   * UPDATE ITEM
   *
   * @param $criteria
   * @param Request $request
   * @return mixed
   */
  public function update($criteria, Request $request)
  {
    \DB::beginTransaction(); //DB Transaction
    try {
      //Get data
      $data = $request->input('attributes');
  
      //Validate Request
      $this->validateRequestApi(new UpdateRequestableRequest((array)$data));

      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);
      $data["reviewed_by"] = $params->user->id;
  
      //Request to Repository
      $oldRequest = $this->service->getItem($criteria, $params);

      if (!$oldRequest) throw new \Exception('Item not found', 404);

      $data["type"] = $oldRequest->type;
      
    //getting update request config
      $requestableConfigs = collect($this->service->moduleConfigs())->keyBy("type");
  
      $requestConfig = $requestableConfigs[$data["type"]];
      $defaultStatus = $requestConfig["defaultStatus"] ?? 0; // 0 = pending
      $eventPath = $requestConfig["events"]["update"] ?? null;
      
      if (isset($data["status"]) && $oldRequest->status != $data["status"]) {
        
          list($response, $newRequest) = $this->updateOrDelete($data["status"], "status", $criteria, $data, $params, $oldRequest, $requestConfig);

          // dispatch status event
          $eventStatusPath = $requestConfig["statusEvents"][$data["status"]] ?? null;
     
         // dd($eventStatusPath);
          if ($eventStatusPath)
            event(new $eventStatusPath($newRequest, $oldRequest, $requestConfig, $params->user));
        
      } else {
        //Request to Repository
        $newRequest = $this->service->updateBy($criteria, $data, $params);
        $response = ["data" => "Item Updated"];
        
      }
      
      // dispatch eta event
      if (isset($data["eta"])) {
        
        if ($oldRequest->eta != $data["eta"]) {
          $eventETAPath = $requestConfig["etaEvent"] ?? null;
          
          if ($eventETAPath)
            event(new $eventETAPath($newRequest, $oldRequest, $requestConfig, $params->user));
        }
      }
      
      if ($eventPath)
        event(new $eventPath($newRequest, $oldRequest, $requestConfig, $params->user));
      
      //Response
      //$response = ["data" => 'Item Updated'];
      
      \DB::commit();//Commit to DataBase
     } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    
    //Return response
    return response()->json($response ?? ["data" => "Item Updated"], $status ?? 200);
  }
  
  private function updateOrDelete($value, $field, $criteria, $data, $params, $oldRequest, $requestConfig)
  {

    //get deleteWhen Code
    switch ($field) {
      case 'status':
        $deleteWhen = $requestConfig["deleteWhenStatus"] ?? false;
        if ($deleteWhen) {
          $deleteWhen = $deleteWhen[$value];
        }
        break;
      
      case 'eta':
        $deleteWhen = $requestConfig["deleteWhenStatus"] ?? false;
        break;
    }

    // if must be deleted
    if ($deleteWhen) {
      $permission = $params->permissions['requestable.requestables.destroy'] ?? false;
      
      // solo si se tiene el permiso para eliminar request o que el request haya sido enviado por el user logueado
      if ($permission || $params->user->id == $oldRequest->created_by) {
        
        //call Method delete
        $this->service->deleteBy($criteria, $params);
        
      } else {
        throw new \Exception('Permission denied', 401);
      }
      
      
      $response = ["data" => "Item Deleted"];
      $newRequest = null;
    } else {
      
      //Request to Repository
      $newRequest = $this->service->updateBy($criteria, $data, $params);
      $response = ["data" => "Item Updated"];
    }
    
    return [
      $response,
      $newRequest
    ];
  }

}