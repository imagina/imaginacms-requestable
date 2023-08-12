<?php

namespace Modules\Requestable\Exports\Reports;

use Modules\Requestable\Entities\Requestable;

class detailedReport
{
  private $params;


  public function __construct($params)
  {
    $this->params = $params;
  }

  /**
   * Return all request
   * @return mixed
   */
  public function getQuery()
  {
    $query = Requestable::query()
      ->without("fields")
      ->selectRaw('
        requestable__requestables.id AS requestable_id,
        category.title AS category,
        status_t.title AS status,
        requestable__requestables.type,
        CONCAT(user_requested_by_requestable.first_name, \' \', user_requested_by_requestable.last_name) AS requested_by,
        CONCAT(user_requestable.first_name, \' \', user_requestable.last_name) AS created_by
    ')->selectSub(function ($query) {
        $query->from('icomments__comments as sub_comments')
          ->selectRaw("CONCAT(sub_comments.comment, ' | Fecha: ', sub_comments.created_at) as last_comment")
          ->whereColumn('sub_comments.commentable_id', 'requestable__requestables.id')
          ->where('sub_comments.commentable_type', 'Modules\\Requestable\\Entities\\Requestable')
          ->orderByDesc('id')
          ->limit(1);
      }, 'last_comment')
      ->leftJoin('requestable__category_translations as category', 'requestable__requestables.category_id', '=', 'category.category_id')
      ->leftJoin('requestable__status_translations as status_t', 'requestable__requestables.status_id', '=', 'status_t.status_id')
      ->leftJoin('users as user_requested_by_requestable', 'requestable__requestables.requested_by', '=', 'user_requested_by_requestable.id')
      ->leftJoin('users as user_requestable', 'requestable__requestables.created_by', '=', 'user_requestable.id')
      ->where('requestable__requestables.category_id', $this->params->filter->categoryId)
      ->orderByDesc('requestable__requestables.id')
      ->orderByDesc('requestable__requestables.created_at');

    return $query;
  }

  /**
   * Make logic to prepare rows
   *
   * @param $rows
   * @param $fields
   * @return mixed
   */
  public function getPrepareRows($rows, $categoryFields)
  {
    //Loop the rows
    $rows = $rows->map(function ($row, $index) use ($categoryFields) {
      //Get only row fields and merge it
      $rowFields = $categoryFields->where("requestable_id", $row->requestable_id)->first();
      if ($rowFields) $row = collect($row->toArray())->merge(collect($rowFields));
      //Move the last_comment of last position
      $lastComment = $row->pull('last_comment');
      $row->put('last_comment', $lastComment);
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
  public function getMap($row)
  {
    $row["last_comment"] = strip_tags($row["last_comment"]);
    //Response
    return $row;
  }

  /**
   * Return the headings without category fields
   * @return array
   */
  public function getHeadings($categoryFields)
  {
    $headers = array_merge([
      'ID',
      trans('requestable::requestables.table.category'),
      trans('requestable::requestables.table.status'),
      trans('requestable::requestables.table.type'),
      trans('requestable::requestables.table.requested by'),
      trans('requestable::requestables.table.created by'),
    ], $categoryFields);
    //Add last comment header
    array_push($headers, trans('requestable::requestables.table.last comment'));

    //Response
    return $headers;
  }
}
