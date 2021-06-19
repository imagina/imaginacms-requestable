<?php

namespace Modules\Requestable\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Internationalisation\BaseFormRequest;

class UpdateRequestableRequest extends BaseFormRequest
{
  private $requestableRepository;
  
  
  public function rules()
    {
  
      $this->requestableRepository = app("Modules\Requestable\Repositories\RequestableRepository");
      $requestableConfig = collect($this->requestableRepository->moduleConfigs());
  
      return [
        'type' => [
          "required",
          Rule::in($requestableConfig->pluck("type")->toArray()),
        ]
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
        'type.in' => "The type is not in the default types defined",
        'type.required' => "The type is required"
      ];
    }

    public function translationMessages()
    {
        return [];
    }
}
