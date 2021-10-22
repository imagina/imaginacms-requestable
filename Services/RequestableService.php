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

class RequestableService extends BaseApiController
{
  private $field;
  private $category;
  private $requestableRepository;
  
  public function __construct(RequestableRepository $requestableRepository, FieldApiController $field, CategoryRepository $category)
  {
    $this->requestableRepository = $requestableRepository;
    $this->field = $field;
    $this->category = $category;
  }
  
  public function create($data){
  
    $params = [
      "filter" => [
        "field" => "type",
      ],
      "include" => [],
      "fields" => [],
    ];
    
    $category = $this->category->getItem($data["type"], json_decode(json_encode($params)));

    if (!isset($category->id)) throw new \Exception('Request Type not found', 400);
    
    $eventPath = $category->events->create ?? null;
  
    $data["status_id"] = $category->defaultStatus()->id;
    $data["requestable_type"] = $category->requestable_type;
    $data["category_id"] = $category->id;
  
    if ($data["requestable_type"] == "Modules\User\Entities\Sentinel\User")
      $data["requestable_id"] = $data["requestable_id"] ?? \Auth::id() ?? null;

    //Create item
    $model = $this->requestableRepository->create($data);

    //Create fields
    if (isset($data["fields"])) {
      foreach ($data["fields"] as $field) {
        $field['requestable_id'] = $model->id;// Add user Id
        $this->validateResponseApi(
          $this->field->create(new Request(['attributes' => (array)$field]))
        );
      }
    }

    if ($model && $eventPath)
      event($event = new $eventPath($model, $category, $model->createdByUser));
  
    
    return $model;
  }
  
  
  public function update($criteria, $data){
  
 
    //Request to Repository
    $oldRequest = $this->requestableRepository->getItem($criteria);
 
    if (!isset($oldRequest->id)) throw new \Exception('Item not found', 404);
  
    $data["type"] = $oldRequest->type;
  
    //getting update request config
    $category = $oldRequest->category;
    
    //finding create event
    $eventPath = $category->events->update ?? null;
 
    //Create or Update fields
    if (isset($data["fields"]))
      foreach ($data["fields"] as $field) {
        if (is_bool($field["value"]) || (isset($field["value"]) && !empty($field["value"]))) {
          $field['requestable_id'] = $oldRequest->id;// Add user Id
          if (!isset($field["id"])) {
            $this->validateResponseApi(
              $this->field->create(new Request(['attributes' => (array)$field]))
            );
          } else {
            $this->validateResponseApi(
              $this->field->update($field["id"], new Request(['attributes' => (array)$field]))
            );
          }
        
        } else {
          if (isset($field['id'])) {
            $this->validateResponseApi(
              $this->field->delete($field['id'], new Request(['attributes' => (array)$field]))
            );
          }
        }
      }
  
    //if the status it's updating
    if (isset($data["status"]) || isset($data["status_id"])){

      //if the data has status will be take it in the value of the category statuses
      //else the status will be take it of the id of the category statuses
      if(isset($data["status"])){
        $status = $category->statuses->where("value", $data["status"])->first();
      }else{
        $status = $category->statuses->where("id", $data["status_id"])->first();
      }
      
      //replacing to the real status id
      $data["status_id"] = $status->id;
      
      //if the status it's different of the old status in the request
      if ($oldRequest->status_id != $status->id) {
      
        //check if the request need to be deleted or just updated
        list($response, $newRequest) = $this->updateOrDelete($status, "status", $criteria, $data, $oldRequest);
      
        // dd($eventStatusPath);
        if (!empty($status->events)){
          $eventStatusPaths = !is_array($status->events) ? [$status->events] : $status->events;
          foreach ($eventStatusPaths as $eventStatusPath){
            event(new $eventStatusPath($newRequest, $oldRequest, $category, $oldRequest->createdByUser));
          }
        }
      }
    }else{
      //if the status doesn't exist in the data just update de request
      $newRequest = $this->requestableRepository->updateBy($criteria, $data);
      $response = ["data" => "Item Updated"];
    }

    // checking eta
    if (isset($data["eta"])) {
    
      //if the eta it's different of the old eta the event will be dispatched
      if ($oldRequest->eta != $data["eta"]) {
        
        $eventETAPath = $category->events->etaUpdated ?? null;

        if ($eventETAPath)
          event(new $eventETAPath($newRequest, $oldRequest, $category, $oldRequest->createdByUser));
      }
    }
  
    //request update event
    if ($eventPath)
      event(new $eventPath($newRequest, $oldRequest, $category, $newRequest->createdByUser ?? $oldRequest->createdByUser));
  
  
  }
  
  private function updateOrDelete($status, $field, $criteria, $data, $oldRequest = null)
  {
  
    //Get Parameters from URL.
    $params = $this->getParamsRequest(request());
    // if must be deleted

    //Request to Repository
    $newRequest = $this->requestableRepository->updateBy($criteria, $data);
    $response = ["data" => "Item Updated"];
    
    if ($status->delete_request) {
      $permission = $params->permissions['requestable.requestables.destroy'] ?? false;

      // solo se permite borrar request si:
      // se tiene el permiso para eliminar requests
      // o que el request haya sido creado por el user que está autenticado
      if ($permission || \Auth::id() == $oldRequest->created_by) {
        
        //call Method delete
        $this->requestableRepository->deleteBy($criteria);
  
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
