<?php

namespace Modules\Requestable\Exports\Reports;

use Modules\Requestable\Entities\Requestable;

class statusesReport
{
  private $params;


  public function __construct($params)
  {
    $this->params = $params;
  }

  /**
   * Return all records by status history and also the request without an status history
   * @return mixed
   */
  public function getQuery()
  {
    $query = Requestable::query()
      ->without("fields")
      ->selectRaw("
        COALESCE(prev_status_t.title, '----') AS previous_status,
        COALESCE(current_status_t.title, no_history_status_t.title, '----') AS current_status,
        COALESCE(current.created_at, requestable__requestables.created_at, '0000-00-00 00:00:00') AS created_at,
        COALESCE(
            CONCAT(user_status_history.first_name, user_status_history.last_name),
            CONCAT(user_requestable.first_name, user_requestable.last_name),
            '----'
        ) AS cambio_realizado_por,
        requestable__requestables.id AS requestable_id,
        COALESCE(CONCAT(user_requested_by_requestable.first_name, user_requested_by_requestable.last_name), '----') AS solicitado_por,
        COALESCE(CONCAT(user_requestable.first_name, user_requestable.last_name), '----') AS peticion_creada_por
    ")
      ->leftJoin('requestable__status_history AS current', 'current.requestable_id', '=', 'requestable__requestables.id')
      ->leftJoin('requestable__status_history AS previous', function ($join) {
        $join->on('current.requestable_id', '=', 'previous.requestable_id')
          ->whereRaw('previous.created_at = (
                SELECT MAX(created_at)
                FROM requestable__status_history AS innerPrevious
                WHERE innerPrevious.requestable_id = current.requestable_id
                AND innerPrevious.created_at < current.created_at
            )');
      })
      ->leftJoin('requestable__status_translations as current_status_t', 'current.status_id', '=', 'current_status_t.status_id')
      ->leftJoin('requestable__status_translations as prev_status_t', 'previous.status_id', '=', 'prev_status_t.status_id')
      ->leftJoin('requestable__status_translations as no_history_status_t', 'requestable__requestables.status_id', '=', 'no_history_status_t.status_id')
      ->leftJoin('users as user_status_history', 'current.created_by', '=', 'user_status_history.id')
      ->leftJoin('users as user_requestable', 'requestable__requestables.created_by', '=', 'user_requestable.id')
      ->leftJoin('users as user_requested_by_requestable', 'requestable__requestables.requested_by', '=', 'user_requested_by_requestable.id')
      ->where('requestable__requestables.category_id', $this->params->filter->categoryId)
      ->orderByDesc('requestable__requestables.id')
      ->orderByDesc('current.created_at');

    return $query;
  }

  /**
   * Make logic to prepare rows
   *
   * @param $rows
   * @param $fields
   * @return mixed
   */
  public function getPrepareRows($rows, $fields)
  {
    //Loop the rows
    $rows = $rows->map(function ($row, $index) use ($fields) {
      //Get only row fields and merge it
      $rowFields = $fields->where("requestable_id", $row->requestable_id)->first();
      if ($rowFields) $row = collect($row->toArray())->merge(collect($rowFields));
      //response
      return $row;
    });

    //Response
    return $rows;
  }
  /**
   * Returns the map for each row, take care with loops maybe cause memory limits with very large reports
   * @param $row
   * @return mixed
   * @throws \Exception
   */
  public function getMap($row){
    //Format created_at
    $date = \Carbon\Carbon::parse($row["created_at"]);
    $row["created_at"] = $date->format('d-m-Y || H:i:s');

    //Response
    return $row;
  }

  /**
   * Return the headings without category fields
   * @return array
   */
  public function getHeadings($categoryFields){
    $headers = array_merge([
      trans('requestable::requestables.table.status old'),
      trans('requestable::requestables.table.status new'),
      trans('requestable::requestables.table.date'),
      trans('requestable::requestables.table.history created by'),
      trans('requestable::requestables.table.id'),
      trans('requestable::requestables.table.requested by'),
      trans('requestable::requestables.table.created by'),
    ], $categoryFields);

    //Response
    return $headers;
  }
}
