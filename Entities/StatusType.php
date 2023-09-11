<?php

namespace Modules\Requestable\Entities;


class StatusType
{
    const INPROGRESS = 0;
    const SUCCESS = 1;
    const FAILED = 2;
   
    private $types = [];

    public function __construct()
    {
        $this->types = [
            self::INPROGRESS => trans('requestable::statuses.types.inprogress'),
            self::SUCCESS=> trans('requestable::statuses.types.success'),
            self::FAILED => trans('requestable::statuses.types.failed'),
        ];
    }

    public function lists()
    {
        return $this->types;
    }

    public function getAllTypes()
    {

        $types = $this->types;
        $typeTransform = [];
        foreach ($types as $key => $type) {
           array_push($typeTransform,['value' => $key, 'name' => $type]);
        }
        return collect($typeTransform);

    }

   
    public function get($typeId)
    {
        if (isset($this->types[$typeId])) {
            return $this->types[$typeId];
        }

        return $this->types[self::INPROGRESS];
    }
    
}
