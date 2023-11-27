<?php

namespace Modules\Requestable\Repositories\Eloquent;

use Modules\Requestable\Repositories\SourceRepository;
use Modules\Core\Icrud\Repositories\Eloquent\EloquentCrudRepository;

class EloquentSourceRepository extends EloquentCrudRepository implements SourceRepository
{
  /**
   * Filter names to replace
   * @var array
   */
  protected $replaceFilters = [];

  /**
   * Relation names to replace
   * @var array
   */
  protected $replaceSyncModelRelations = [];

  /**
   * Filter query
   *
   * @param $query
   * @param $filter
   * @param $params
   * @return mixed
   */
  public function filterQuery($query, $filter, $params)
  {

    /**
     * Note: Add filter name to replaceFilters attribute before replace it
     *
     * Example filter Query
     * if (isset($filter->status)) $query->where('status', $filter->status);
     *
     */

     //Not permission source index all | exist but is false
    if (!isset($params->permissions['requestable.sources.index-all']) || (!$params->permissions['requestable.sources.index-all'])) {

      if (isset($params->user)) {
        $user = $params->user;

        $query->whereRaw("requestable__sources.id IN (SELECT source_id from requestable__user_source where user_id = ".$user->id . ")");
       
      }
      
    }

    //dd($query->toSql());

    //Response
    return $query;
  }

  /**
   * Method to sync Model Relations
   *
   * @param $model ,$data
   * @return $model
   */
  public function syncModelRelations($model, $data)
  {
    //Get model relations data from attribute of model
    $modelRelationsData = ($model->modelRelations ?? []);

    /**
     * Note: Add relation name to replaceSyncModelRelations attribute before replace it
     *
     * Example to sync relations
     * if (array_key_exists(<relationName>, $data)){
     *    $model->setRelation(<relationName>, $model-><relationName>()->sync($data[<relationName>]));
     * }
     *
     */

    //Response
    return $model;
  }
}
