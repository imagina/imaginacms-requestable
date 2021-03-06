<?php

namespace Modules\Requestable\Http\Controllers\Api;

use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Illuminate\Contracts\Foundation\Application;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Requestable\Http\Requests\CreateRequestableRequest;
use Modules\Requestable\Http\Requests\UpdateRequestableRequest;
use Modules\Requestable\Repositories\CategoryRepository;
use Modules\Requestable\Repositories\FieldRepository;
use Modules\Requestable\Repositories\RequestableRepository;
use Modules\Requestable\Services\RequestableService;
use Modules\Requestable\Transformers\RequestableTransformer;


class RequestableApiController extends BaseApiController
{
  private $requestable;
  private $service;
  
  public function __construct(RequestableRepository $requestable, RequestableService $service)
  {
    parent::__construct();
    $this->requestable = $requestable;
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
      $newRequest = $this->requestable->getItemsBy($params);
      
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
      //$newRequest = $this->requestable->getItemsBy($params);
      
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
      $newRequest = $this->requestable->getItem($criteria, $params);
      
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
      
      //Validate Request
      $this->validateRequestApi(new CreateRequestableRequest((array)$data));
      
      $model = $this->service->create($data);
      
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

      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);

      //Get data
      $data = $request->input('attributes');
      
      //Validate Request
      $this->validateRequestApi(new UpdateRequestableRequest((array)$data));

      $model = $this->service->update($criteria,$data,$params);
      
      //Response
      //$response = ["data" => 'Item Updated'];
      
      \DB::commit();//Commit to DataBase
    } catch (\Exception $e) {
      //dd($e);
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
  
    }
    
    //Return response
    return response()->json($response ?? ["data" => "Item Updated"], $status ?? 200);
  }
  
 
  
}