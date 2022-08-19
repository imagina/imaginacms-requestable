<?php


namespace Modules\Requestable\Services;

use Modules\Requestable\Repositories\StatusRepository;


class StatusService
{
 
  private $statusRepository;
  
  public function __construct(
    StatusRepository $statusRepository
  ){
   
    $this->statusRepository = $statusRepository;
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
  
}
