<?php

namespace Modules\Requestable\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;
use Illuminate\Validation\Rule;
use Modules\Requestable\Rules\ValidateType;

class CreateStatusRequest extends BaseFormRequest
{
    public function rules()
    {   
        $categoryService = app("Modules\Requestable\Services\CategoryService");

        return [
            'category_id' => "required",
            'value' => Rule::requiredIf($categoryService->isInternal($this->category_id)),
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
        return [
            'category_id.required' => trans('requestable::common.messages.field required'),
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
