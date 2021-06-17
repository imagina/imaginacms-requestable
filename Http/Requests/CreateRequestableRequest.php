<?php

namespace Modules\Requestable\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Internationalisation\BaseFormRequest;

class CreateRequestableRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
          'type' => [
            Rule::in(["joinToTeamRequest"]),
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
        'type' => "The type is not in the default types defined"
      ];
    }

    public function translationMessages()
    {
        return [];
    }
}
