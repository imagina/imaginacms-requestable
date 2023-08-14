<?php

namespace Modules\Requestable\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;

//Events
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;

use Modules\Isite\Traits\ReportQueueTrait;

class RequestablesExport implements ShouldQueue,
  FromQuery, ShouldAutoSize, WithEvents, WithHeadings, WithMapping
{
  use Exportable, ReportQueueTrait;

  private $params;
  private $exportParams;
  private $categoryFields;
  private $includeCategoryFields;
  private $reportType;
  private $log;

  public function __construct($params, $exportParams)
  {
    $this->userId = \Auth::id();//Set for ReportQueue
    $this->params = $params;
    $this->log = "Requestable:: Exports|" . $this->params->filter->reportType . "Report";
    \Log::info("$this->log|Init");
    $this->exportParams = $exportParams;
    $this->includeCategoryFields = (boolean)setting('requestable::showExtraFieldsFromFormInReport');
    $this->categoryFields = collect([]);
    $this->getCategoryFields();
    //Instance the report type
    $this->reportType = app('Modules\Requestable\Exports\Reports\\' . $this->params->filter->reportType . 'Report', ['params' => $params]);
  }

  /**
   * Get the category fields
   * @return void
   */
  public function getCategoryFields()
  {
    if ($this->includeCategoryFields) {
      \Log::info("$this->log|CategoryFields");
      $categoryRepository = app('Modules\Requestable\Repositories\CategoryRepository');
      //Get request category by filter
      $params = ['include' => ['forms.fields']];
      $category = $categoryRepository->getItem($this->params->filter->categoryId, json_decode(json_encode($params)));
      //Get and instance the category fields
      $this->categoryFields = $category->form->fields->map->only('name', 'label', 'type');
    }
  }

  /**
   * Get query by report type
   * @return mixed
   */
  public function query()
  {
    $query = $this->reportType->getQuery();
    return $query;
  }

  /**
   * Request the fields of all rows and merge each one with the row by chunks
   * NOTE: the query sorts the fields with the correct format to merge directly with the row
   * do not use loops because this causes memory limits with very large reports
   * @param $rows
   * @return mixed
   */
  public function prepareRows($rows)
  {
    $fields = collect([]);
    //Get the category fields to each row
    if ($this->includeCategoryFields) {
      //Make query to get all fields by requestable id
      $fieldNames = $this->categoryFields->pluck("name")->toArray();
      $selectExpressions = ["f.entity_id as requestable_id"];
      foreach ($fieldNames as $field) {
        $selectExpressions[] = \DB::raw("MAX(CASE WHEN f.name = '$field' THEN ft.value END) AS `$field`");
      }
      //Request all fields by requestable id
      $fields = \DB::table('ifillable__fields as f')
        ->select($selectExpressions)
        ->leftJoin('ifillable__field_translations as ft', 'f.id', '=', 'ft.field_id')
        ->whereIn('f.name', $fieldNames)
        ->where('f.entity_type', 'Modules\\Requestable\\Entities\\Requestable')
        ->whereIn('f.entity_id', $rows->pluck('requestable_id')->unique()->toArray())
        ->groupBy('f.entity_id')
        ->orderBy('f.entity_id', 'desc')
        ->get();
    }

    //Get prepareRows by reportType
    $rows = $this->reportType->getPrepareRows($rows, $fields);

    //Response
    return collect($rows->toArray());
  }

  /**
   * Get map by report type
   * @param $row
   * @return array
   */
  public function map($row): array
  {
    $mapRow = $this->reportType->getMap($row);
    return $mapRow;
  }

  /**
   * Get the main headers by reportType and merge the categoryFields
   * @return array
   */
  public function headings(): array
  {
    \Log::info("$this->log|Headings");
    //Get category fields columns
    $categoryFields = $this->categoryFields->pluck("label")->toArray();
    //Get headings
    $headers = $this->reportType->getHeadings($categoryFields);
    \Log::info("$this->log|MappingRows");

    //Response
    return $headers;
  }

  public function registerEvents(): array
  {
    return [
      //Event gets raised just after the sheet is created.
      BeforeSheet::class => function (BeforeSheet $event) {
        \Log::info("$this->log|BeforeSheet");
        $this->lockReport($this->exportParams->exportName);
      },

      // Event gets raised at the end of the sheet process
      AfterSheet::class => function (AfterSheet $event) {
        \Log::info("$this->log|AfterSheet: Exported");
        $this->unlockReport($this->exportParams->exportName);
        $event->getSheet()->getDelegate()->getStyle(1)->getFont()->setBold(true);
      },
    ];
  }
}
