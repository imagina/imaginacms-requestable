<?php

namespace Modules\Requestable\Http\Controllers\Api;

use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Illuminate\Contracts\Foundation\Application;
use Modules\Ichat\Transformers\ConversationTransformer;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Requestable\Entities\Requestable;
use Modules\Requestable\Http\Requests\CreateRequestableRequest;
use Modules\Requestable\Http\Requests\UpdateRequestableRequest;
use Modules\Requestable\Repositories\CategoryRepository;
use Modules\Requestable\Repositories\FieldRepository;
use Modules\Requestable\Repositories\RequestableRepository;
use Modules\Requestable\Services\RequestableService;
use Modules\Requestable\Transformers\RequestableTransformer;
use Modules\Core\Icrud\Controllers\BaseCrudController;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use ReflectionClass;

// Events
use Modules\Requestable\Events\RequestableWasCreated;
use Modules\Requestable\Events\RequestableWasUpdated;
use Modules\Requestable\Events\RequestableIsUpdating;

class RequestableApiController extends BaseCrudController
{
  private $requestable;
  private $service;
  public $model;
  public $modelRepository;

  public function __construct(RequestableRepository $requestable, RequestableService $service, Requestable $model)
  {
    parent::__construct();
    $this->requestable = $requestable;
    $this->service = $service;
    $this->model = $model;
    $this->modelRepository = $requestable;
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
      $response = ["messages" => [["message" => $e->getMessage(), "type" => "error"]]];
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
      $response = ["messages" => [["message" => $e->getMessage(), "type" => "error"]]];
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
      $response = ["messages" => [["message" => $e->getMessage(), "type" => "error"]]];
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

      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);

      //Get data
      $data = $request->input('attributes');

      //Validate Request
      $this->validateRequestApi(new CreateRequestableRequest((array)$data));

      //Validate with Permission
      $data = $this->service->validateCreatedBy($data, $params);

      $model = $this->service->create($data);

      event(new RequestableWasCreated($model));

      //Response
      $response = ["data" => new RequestableTransformer($model)];

      \DB::commit(); //Commit to Data Base
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["messages" => [["message" => $e->getMessage(), "type" => "error"]]];

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

      $requestableRepository = app('Modules\Requestable\Repositories\RequestableRepository');
      $requestableInDB = $requestableRepository->getItem($criteria);

      event(new RequestableIsUpdating($data, $requestableInDB));

      //Validate Request
      $this->validateRequestApi(new UpdateRequestableRequest((array)$data));

      //Validate with Permission
      $data = $this->service->validateCreatedBy($data, $params);

      $model = $this->service->update($criteria, $data, $params);

      event(new RequestableWasUpdated($model));

      //Response
      //$response = ["data" => 'Item Updated'];

      \DB::commit();//Commit to DataBase
    } catch (\Exception $e) {
      //dd($e);
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["messages" => [["message" => $e->getMessage(), "type" => "error"]]];

    }

    //Return response
    return response()->json($response ?? ["data" => "Item Updated"], $status ?? 200);
  }

  /**
   * Add comment to requestable
   * @param $criteria (requestable id)
   * @param $request
   */
  public function addComment($criteria, Request $request)
  {

    \DB::beginTransaction(); //DB Transaction
    try {

      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);

      //Get data
      $data = $request->input('attributes');

      //Validate Request
      $this->validateRequestApi(new \Modules\Icomments\Http\Requests\CreateCommentRequest((array)$data));

      // Search
      $model = $this->requestable->getItem($criteria, $params);

      //Break if no found item
      if (!$model) throw new \Exception('Item not found', 404);

      //Create comment
      $comment = app('Modules\Icomments\Services\CommentService')->create($model, $data);

      //Response
      $response = ["data" => new \Modules\Icomments\Transformers\CommentTransformer($comment)];

      \DB::commit();//Commit to DataBase
    } catch (\Exception $e) {
      //dd($e);
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["messages" => [["message" => $e->getMessage(), "type" => "error"]]];

    }

    //Return response
    return response()->json($response ?? ["data" => "Comment Added"], $status ?? 200);
  }

  public function analytics($criteria, Request $request)
  {
    \DB::beginTransaction(); //DB Transaction
    try {
      $params = $this->getParamsRequest($request);
      $requestableRepository = app('Modules\Requestable\Repositories\RequestableRepository');
      $functionsInRepo = new ReflectionClass('Modules\Requestable\Repositories\RequestableRepository');
      $existFunction = $functionsInRepo->hasMethod($criteria);
      if ($existFunction) {
        $data = $requestableRepository->{$criteria}($params);
      } else {
        $data = ["errors" => trans('Requestable::common.erros.nonExistentFunction')];
        $status = 404;
      }
      \DB::commit();//Commit to DataBase
    } catch (\Exception $e) {
      //dd($e);
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["messages" => [["message" => $e->getMessage(), "type" => "error"]]];

    }
    //Return response
    return response()->json($data, $status ?? 200);
  }
  
  public function createConversation($criteria, Request $request){
  
    \DB::beginTransaction(); //DB Transaction
    try {
      $params = $this->getParamsRequest($request);
  
      $requestable = $this->requestable->getItem($criteria);
      
      if(!isset($requestable->id)){
        throw new \Exception(trans("requestable::requestables.validations.chatRequestableIdRequired"),400);
      }
      $requestedBy = $requestable->requestedBy;
  
      if(!isset($requestedBy->id)){
        throw new \Exception(trans("requestable::requestables.validations.chatRequestedByIdRequired"),400);
      }
  
      if(empty($requestedBy->phone)){
        throw new \Exception(trans("requestable::requestables.validations.chatRequestedByPhoneNumberRequired"),400);
      }
      
      if(isset($requestable->conversation->id)){
        $conversation = $requestable->conversation;
      }else{
        $conversation = $requestable->createConversation([
          'private' => false,
          'provider_type' => 'whatsapp',
          'provider_id' => $requestedBy->phone,
          'entity_id' => $requestable->id,
          'entity_type' => get_class($requestable),
          'users' => [$requestedBy->id]
        ]);
      }
  
      //Response
      $response = ["data" => new ConversationTransformer($conversation)];
      
      \DB::commit();//Commit to DataBase
    } catch (\Exception $e) {
      //dd($e);
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["messages" => [["message" => $e->getMessage(), "type" => "error"]]];
    
    }
    //Return response
    return response()->json($response, $status ?? 200);
  
  }
}