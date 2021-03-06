<?php


namespace Modules\Requestable\Services;

use Modules\Icommerce\Events\OrderWasCreated;
use Modules\Icommerce\Repositories\CartRepository;
use Modules\Icommerce\Repositories\OrderRepository;
use Modules\Icommerce\Support\OrderHistory as orderHistorySupport;
use Modules\Icommerce\Support\OrderItem as orderItemSupport;
use Modules\Icommerce\Support\Coupon as validateCoupons;
use Modules\Iprofile\Repositories\UserApiRepository;
use Modules\Icommerce\Repositories\CartProductRepository;
use Illuminate\Http\Request;
use Modules\Requestable\Http\Controllers\Api\FieldApiController;
use Modules\Requestable\Repositories\CategoryRepository;
use Modules\Requestable\Repositories\FieldRepository;
use Modules\Requestable\Repositories\RequestableRepository;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Icomments\Services\CommentService;

class RequestableService extends BaseApiController
{
  private $field;
  private $category;
  private $commentService;
  private $requestableRepository;
  
  public function __construct(RequestableRepository $requestableRepository, FieldApiController $field, CategoryRepository $category, CommentService $commentService)
  {
    $this->requestableRepository = $requestableRepository;
    $this->field = $field;
    $this->category = $category;
    $this->commentService = $commentService;
  }
  
  public function create($data)
  {
    
    $params = [
      "filter" => [
        "field" => "type",
      ],
      "include" => [],
      "fields" => [],
    ];
    
    $category = $this->category->getItem($data["type"], json_decode(json_encode($params)));
    
    if (!isset($category->id)) throw new \Exception('Request Type not found', 400);
    
    $eventPath = $category->events["create"] ?? null;
    
    $data["status_id"] = $category->defaultStatus()->id;
    $data["requestable_type"] = $category->requestable_type;
    $data["category_id"] = $category->id;
    
    if ($data["requestable_type"] == "Modules\User\Entities\Sentinel\User")
      $data["requestable_id"] = $data["requestable_id"] ?? \Auth::id() ?? null;
    
    //Create item
    $model = $this->requestableRepository->create($data);
 
    
    if ($model && $eventPath)
      event($event = new $eventPath($model));
    
    
    return $model;
  }
  
  
  public function update($criteria, $data,$params = null)
  {

    //Request to Repository
    $oldRequest = $this->requestableRepository->getItem($criteria,$params);
    
    
    if (!isset($oldRequest->id)) throw new \Exception('Item not found', 404);
    
    $data["type"] = $oldRequest->type;
    
    //getting update request config
    $category = $oldRequest->category;
  
    //if the data has status will be take it in the value of the category statuses
    //else the status will be take it of the id of the category statuses
    if (isset($data["status"])) {
      $status = $category->statuses->where("value", $data["status"])->first();
    } else {
      $status = $category->statuses->where("id", $data["status_id"])->first();
    }
    
    //check if the request need to be deleted or just updated because some statuses could need to delete the request
    list($response, $newRequest) = $this->updateOrDelete($criteria, $data,$status ?? null, $oldRequest);
  
    //if the status it's updating
    if (isset($data["status"]) || isset($data["status_id"])) {
  
      //replacing to the real status id
      $data["status_id"] = $status->id;
      //if the status it's different of the old status in the request, will be dispatch the status event if exist
      if ($oldRequest->status_id != $status->id) {
        
        //default status updated comment
        $this->commentService->create($oldRequest,["comment" => trans("requestable::statuses.comments.statusUpdated",["prevStatus" => $oldRequest->status->title,"postStatus" =>  $status->title])]);
  
        //custom comment to the status updated
        if(isset($data["comment"]) && !empty($data["comment"])){
          $this->commentService->create($oldRequest,["comment" => $data["comment"]]);
        }
        
        if (!empty($status->events)) {
          $eventStatusPaths = !is_array($status->events) ? [$status->events] : $status->events;
          
          foreach ($eventStatusPaths as $eventStatusPath) {
            event(new $eventStatusPath($newRequest, $oldRequest, $oldRequest->createdByUser));
          }
        }
      }
    }
    
   
    // checking eta
    if (isset($data["eta"])) {
      
      //if the eta it's different of the old eta the event will be dispatched
      if ($oldRequest->eta != $data["eta"]) {
        
        $eventETAPath = $category->events["etaUpdated"] ?? null;
        
        if ($eventETAPath)
          event(new $eventETAPath($newRequest, $oldRequest));
      }
    }
  
    //finding create event
    $eventPath = $category->events["update"] ?? null;
  
    //request update event
    if ($eventPath)
      event(new $eventPath($newRequest, $oldRequest));
    
    
  }
  
  private function updateOrDelete($criteria, $data, $status = null, $oldRequest = null)
  {
    
    //Get Parameters from URL.
    $params = $this->getParamsRequest(request());
    // if must be deleted
    
    //Request to Repository
    $newRequest = $this->requestableRepository->updateBy($criteria, $data);
    $response = ["data" => "Item Updated"];
    
    if (isset($status->delete_request) && $status->delete_request) {
      $permission = $params->permissions['requestable.requestables.destroy'] ?? false;
      
      // solo se permite borrar request si:
      // se tiene el permiso para eliminar requests
      // o que el request haya sido creado por el user que est?? autenticado
      if ($permission || \Auth::id() == $oldRequest->created_by) {
        
        //call Method delete
        $this->requestableRepository->deleteBy($criteria,$params);
  
        $deletedEvent = $oldRequest->category->events["delete"] ?? null;

        if ($deletedEvent)
          event(new $deletedEvent($newRequest,$oldRequest));
        
        $response = ["data" => "Item Deleted"];
        $newRequest = null;
      } else {
        throw new \Exception('Permission denied', 401);
      }
    }
    
    return [
      $response,
      $newRequest
    ];
  }
  
}
