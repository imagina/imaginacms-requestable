<?php

namespace Modules\Requestable\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;

//Events
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

//Entities
use Modules\Requestable\Entities\Category;
use Modules\Media\Entities\File;

//Extra
use Modules\Notification\Services\Inotification;


class RequestablesExport implements FromQuery, 
WithEvents, ShouldQueue, WithMapping, WithHeadings
{
  use Exportable;

  private $params;
  private $exportParams;
  private $inotification;
  private $requestableRepository;

  private $category = null;
  private $fields = null;

  
  public function __construct($params, $exportParams)
  {
    $this->params = $params;
    $this->exportParams = $exportParams;
    $this->inotification = app('Modules\Notification\Services\Inotification');
    $this->requestableRepository = app('Modules\Requestable\Repositories\RequestableRepository');

    $this->getExtraFields();
  }

  /*
  * Get items from repository
  */
  public function query()
  {
    
    $this->params->onlyQuery = true;

    $order['field'] = 'id';
    $this->params->filter->order = (object)$order;

    return $this->requestableRepository->getItemsBy($this->params);

  }

  /*
  * Get Category from filter params
  */
  public function getCategory(){

    $filter = $this->params->filter ?? null;
    if(!is_null($filter)){
      if(isset($filter->categoryId)){
        $this->category = Category::find($filter->categoryId);
      }
    }
    
  }

  /*
  * Get Fields from Form Category
  */
  public function getExtraFields(){

    // Get Category From Filter Params
    $this->getCategory();

    // Validate category and get fields from Form category
    if(!is_null($this->category) && !is_null($this->category->form)){
      $fieldsAll = $this->category->form->fields;
      $this->fields = $fieldsAll->map->only('name','label','type');
    }

  }

  /*
  * Get fields and add to item
  */
  public function addFieldsToItem($item,$baseItem){
    
    //Extra fields
    if(!is_null($this->fields)){
      
      foreach($this->fields as $key => $field){

        $value = '--';

        /* Check if field from Form is a "file"
        and change it because in the fillable they save it as "medias_single"
        */
        if($field['type']=="12")
          $field['name'] = "medias_single";

        // Convert to snake case fields from Form
        $nameSnake = \Str::snake($field['name']);

        // Validate field exist in item
        $fieldItem = $item->fields->where('name',$nameSnake)->first();
       
        // Get field Value
        if(!is_null($fieldItem)){

          $value = $fieldItem->translations->first()->value ?? '--';
          
          // Value from Medias Single
          if($field['name']=="medias_single"){
            if(isset($value['mainimage']) && !is_null($value['mainimage'])){
              $file = File::find($value['mainimage']);
              $value = $file->pathString ?? '--';
            }else{
              $value = '--';
            }
          }
          
        }

        //Add extra field value
        array_push($baseItem, $value);
      }

    }

    return $baseItem;
      
  }

  /*
  * Get Last comment and add to item
  */
  public function addLastCommentToItem($item,$baseItem){

    $formatComment = "--";

    if(count($item->comments)>0){

      $lastComment = $item->comments->last();
      $formatComment = $lastComment->comment." | Fecha: ".$lastComment->created_at->format('d-m-Y');
    }

    array_push($baseItem, $formatComment);

    return $baseItem;

  }

  /**
  * Table headings
  * @return string[]
  */
  public function headings(): array
  {
    
    // Base Fields
    $baseFields = [
      'ID',
      trans('requestable::requestables.table.category'),
      trans('requestable::requestables.table.status'),
      trans('requestable::requestables.table.type'),
      trans('requestable::requestables.table.created by')
    ];

    //Extra fields
    if(!is_null($this->fields)){
      foreach ($this->fields as $key => $field) {
        array_push($baseFields, str_replace('*','',$field['label']));
      }
    }

    //Add only last comment
    array_push($baseFields, trans('requestable::requestables.table.last comment'));

    return $baseFields;
    
  }

  /**
  * Each Item
  */
  public function map($item): array
  {
    
    // Base Item Fields
    $baseItem = [
      $item->id ?? null,
      $item->category->title ?? null,
      $item->status->title ?? null,
      $item->type ?? null,
      $item->createdByUser->present()->fullname ?? null
    ];

    //Extra Fields
    $baseItem = $this->addFieldsToItem($item,$baseItem);

    //Last Comment
    $baseItem = $this->addLastCommentToItem($item,$baseItem);
    
    return $baseItem;

  }

  /**
  * Handling Events
  * @return array
  */
  public function registerEvents(): array
  {
    return [
      // Event gets raised at the end of the sheet process
      AfterSheet::class => function (AfterSheet $event) {
        //Send pusher notification
        $this->inotification->to(['broadcast' => $this->params->user->id])->push([
          "title" => "New report",
          "message" => "Your report is ready!",
          "link" => url(''),
          "isAction" => true,
          "frontEvent" => [
            "name" => "isite.export.ready",
            "data" => $this->exportParams
          ],
          "setting" => ["saveInDatabase" => 1]
        ]);
      },
    ];
  }

}
