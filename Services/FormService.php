<?php


namespace Modules\Requestable\Services;


class FormService
{

  public function create(string $systemName)
  {

    $formRepository = app("Modules\Iforms\Repositories\FormRepository");
    $blockRepository = app("Modules\Iforms\Repositories\BlockRepository");

    $params = [
      "filter" => ["field" => "system_name"],
      "include" => [],
      "fields" => [],
    ];

    $form = $formRepository->getItem($systemName, json_decode(json_encode($params)));

    //Validation Form
    if (!isset($form->id)) {

      try {

        // Create Form
        $form = $formRepository->create([
          "title" => trans("requestable::forms.lead.title"),
          "system_name" => $systemName,
          "active" => true
        ]);

        $options["urlTermsAndConditions"] = null;

        // Create Block
        $block = $blockRepository->create([
          "form_id" => $form->id
        ]);

        // Create Field - Text
        $field = $this->createField($form->id, $block->id, 1, "name", false, "requestable::forms.lead.fields.name");

        // Create Field - Text
        $field = $this->createField($form->id, $block->id, 1, "lastname", false, "requestable::forms.lead.fields.lastname");

        // Create Field - Phone
        $field = $this->createField($form->id, $block->id, 10, "telephone", false, "requestable::forms.lead.fields.telephone");
        $options["replyToMobile"] = $field->id;

        // Create Field - Email
        $field = $this->createField($form->id, $block->id, 4, "email", false, "requestable::forms.lead.fields.email");
        $options["replyTo"] = $field->id;

        // Create Field - Textfield
        $field = $this->createField($form->id, $block->id, 2, "comment", false, "requestable::forms.lead.fields.comment");

        //Create Field - Text
        $field = $this->createField($form->id, $block->id, 1, "value", false, "requestable::forms.lead.fields.value");

        //Update form with options
        $form->options = $options;
        $form->save();


        return $form;

      } catch (\Exception $e) {
        \Log::error($this->log . 'Message:' . $e->getMessage());
      }

    }

  }

  /*
  * Create Field
  */
  public function createField($formId, $blockId, $type, $name, $required, $label)
  {

    $fieldRepository = app("Modules\Iforms\Repositories\FieldRepository");

    $dataToCreate = [
      "form_id" => $formId,
      "block_id" => $blockId,
      "type" => $type,
      "name" => $name,
      "required" => $required,
      "es" => ['label' => trans($label, [], 'es')],
      "en" => ['label' => trans($label, [], 'en')]
    ];

    // Create Field
    $fieldCreated = $fieldRepository->create($dataToCreate);

    dd('aca esta services', $fieldCreated);

    if ($fieldCreated->name == 'comment' || $fieldCreated->name == 'value') {
      \DB::table('iforms__fields')->where('id', $fieldCreated->id)->update(['system_type' => 'requestableField-' . $fieldCreated->name]);
    } else {
      \DB::table('iforms__fields')->where('id', $fieldCreated->id)->update(['system_type' => 'requestableHiddenField-' . $fieldCreated->name]);
    }

    return $fieldCreated;

  }


}
