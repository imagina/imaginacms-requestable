<?php

namespace Modules\Requestable\Events;

class CategoryWasCreated
{
    
    public $params;
   
    public function __construct($params = null)
    {
        $this->params = $params;
    }

}