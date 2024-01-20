<?php

namespace Modules\Requestable\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CreateCategoryRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            //'type' => 'required|min:5|unique:requestable__categories'
        ];
    }

    public function translationRules()
    {
        return [
          'title' => 'required|min:2',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'type.required' => trans('requestable::common.messages.field required'),
            'type.unique' => trans('requestable::common.messages.field unique',['field'=>'TIPO']),
        ];
    }

    public function translationMessages()
    {
        return [
          // title
          'title.required' => trans('icommerce::common.messages.field required'),
          'title.min:2' => trans('icommerce::common.messages.min 2 characters'),
        ];
    }

    public function getValidator(){
        return $this->getValidatorInstance();
    }

}
