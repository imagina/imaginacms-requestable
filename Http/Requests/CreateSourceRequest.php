<?php

namespace Modules\Requestable\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CreateSourceRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'title' => 'required',
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
        return [
            'title.required' => trans('requestable::common.messages.field required'),
        ];
    }

    public function translationMessages()
    {
        return [];
    }

    public function getValidator(){
        return $this->getValidatorInstance();
    }
    
}
