<?php


namespace Modules\Requestable\Services;

use Modules\Requestable\Repositories\StatusRepository;
use Modules\Requestable\Entities\DefaultStatus;

class StatusService
{
 
  private $statusRepository;
  
  public function __construct(
    StatusRepository $statusRepository
  ){
   
    $this->statusRepository = $statusRepository;
  }
  
  /**
   * Check if status to delete is Default
   * @param $criteria(id to search)
  */
  public function checkIfStatusIsDefault($criteria,$params){

    $model = $this->statusRepository->getItem($criteria, $params);

    //Throw exception if no found item
    if (!$model) throw new \Exception('Item not found', 204);

    //The status is default
    if($model->default){

      //Buscar el status hermano mas antiguo creado y que no sea final
      $params = [
        'filter'=>['category_id' => $model->category_id,'final' => 0,'default' => 0],
        'order' => ['way' => 'asc'],
        'take'=> 1
      ];
      $statuses = $this->statusRepository->getItemsBy(json_decode(json_encode($params)));

      if(!is_null($statuses)){

        //\Log::info("Requestable - Service|StatusService|checkIfStatusIsDefault|status id ".$statuses[0]->id);

        // New status like default for this category
        $statuses[0]->default = 1;
        $statuses[0]->save();

      }

    }

  }


  /**
  * Update order position to Requests
  * @param $data (array)
  * @param $params (from Request)
  */
  public function updateOrderPosition($data,$params){


    foreach ($data as $position => $item) {

      $status = $this->statusRepository->getItem($item['id'],json_decode(json_encode($params)));

      if(!is_null($status))
        $this->statusRepository->update($status, ['position' => $position]);

      
    }
  }

  /**
   * Create statuses from Config or Category
   * @param $config (Data with statuses and other infor)
   */
  public function createStatuses(array $config, object $category)
  {

    //Add default Statuses
    if(isset($config["useDefaultStatuses"]) && $config["useDefaultStatuses"]){
      $statuses = (new DefaultStatus())->lists();
    }else{
      $statuses = $config["statuses"];
    }

    
    // Create Status
    foreach ($statuses as $key => $status) {
 
      $this->statusRepository->create([
          "category_id" => $category->id,
          'value' => $key,
          'final' => $status["final"] ?? false,
          'default' => $config["defaultStatus"] ?? $status["default"] ?? false,
          'cancelled_elapsed_time' => $config["statusToSetWhenElapsedTime"] ?? $status["cancelled_elapsed_time"] ?? false,
          'events' => $config["eventsWhenStatus"][$key] ?? $status["events"] ?? null,
          'delete_request' => $config["deleteWhenStatus"][$key] ??  $status["delete_request"] ?? false,
          'es' => ["title" => trans($status["title"],[],'es')],
          'en' => ["title" => trans($status["title"],[],'en')],
          'type' => $status["type"] ?? 0, 
          'color' => $status["color"] ?? '#3f36eb'
        ] 
      );

    }

  }

  /**
   * Validation when delete
   */
  public function hasRequests($criteria,$params)
  {

    $status = $this->statusRepository->getItem($criteria, $params);

    //Throw exception if no found item
    if (!$status) throw new \Exception(trans("requestable::common.notFound"), 204);

    //Best performance than 'requests->count()'
    if($status->requests()->count()>0)
      throw new \Exception(trans("requestable::statuses.validation.associatedRequests"), 409);
   
  }
  
}
