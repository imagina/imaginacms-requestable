<?php

namespace Modules\Requestable\Events;

class RequestableIsUpdating
{

  public $requestable;
  public $data;


  public function __construct($data, $requestable)
  {

    $this->requestable = $requestable;
    $this->data = $data;

  }

}