<?php

namespace Modules\Requestable\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;

class ValidateType implements Rule, DataAwareRule
{

    protected $data = [];
    private $statusRepository;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->statusRepository = app("Modules\Requestable\Repositories\StatusRepository");
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute - Example: type
     * @param  mixed  $value - Example "1". 
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $typeValue = (int)$value;

        //Check Status Success
        if($typeValue==2)
            return $this->existsStatusWithType($typeValue); 
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('requestable::statuses.validation.validateType');
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Check if exist a status with the same type
     */
    public function existsStatusWithType($type)
    {
        
        $params = [
            "filter" => [
                "type" => $type,
                "category_id" => (int)$this->data['category_id']
            ]
        ];
        $result = $this->statusRepository->getItemsBy(json_decode(json_encode($params)));

        if(count($result)>0)
            return false; //cannot create or update
        else
            return true;
        
    }

}