<?php

namespace Modules\Requestable\Http\Requests;
use Modules\Requestable\Rules\ValidateType;

use Modules\Core\Internationalisation\BaseFormRequest;

class UpdateStatusRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'category_id' => "required",
            'type' => ['sometimes', new ValidateType]
        ];
    }

    public function translationRules()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }

    public function translationMessages()
    {
        return [];
    }

    public function getValidator(){
        return $this->getValidatorInstance();
    }

}
