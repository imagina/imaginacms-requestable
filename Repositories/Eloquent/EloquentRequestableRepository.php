<?php

namespace Modules\Requestable\Repositories\Eloquent;

use Modules\Requestable\Repositories\RequestableRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentRequestableRepository extends EloquentBaseRepository implements RequestableRepository
{
  public function getItemsBy($params = false)
  {
    /*== initialize query ==*/
    $query = $this->model->query();
    
    /*== RELATIONSHIPS ==*/
    if (in_array('*', $params->include)) {//If Request all relationships
      $query->with([]);
    } else {//Especific relationships
      $includeDefault = [];//Default relationships
      if (isset($params->include))//merge relations with default relationships
        $includeDefault = array_merge($includeDefault, $params->include);
      $query->with($includeDefault);//Add Relationships to query
    }
    
    /*== FILTERS ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;//Short filter
      
      //Filter by date
      if (isset($filter->date)) {
        $date = $filter->date;//Short filter date
        $date->field = $date->field ?? 'created_at';
        if (isset($date->from))//From a date
          $query->whereDate($date->field, '>=', $date->from);
        if (isset($date->to))//to a date
          $query->whereDate($date->field, '<=', $date->to);
      }
  
  
      //by related id
      if (isset($filter->requestableId)) {
        $query->where('requestable_id', $filter->requestableId);
      }
      //by related name
      if (isset($filter->requestableType)) {
        $query->where('requestable_type', $filter->requestableType);
      }
      
      if (isset($filter->createdBy)) {
        $query->where('created_by', $filter->createdBy);
      }
      
      //by type
      if(isset($filter->type) && $filter->type) {
        if(!is_array($filter->type)) $filter->type = [$filter->type];
        $query->whereIn("type",$filter->type);
      }
  
      //by status
      if(isset($filter->status) && $filter->status) {
        $query->whereIn("status",$filter->status);
      }
  
      //Order by
      if (isset($filter->order)) {
        $orderByField = $filter->order->field ?? 'created_at';//Default field
        $orderWay = $filter->order->way ?? 'desc';//Default way
        $query->orderBy($orderByField, $orderWay);//Add order to query
      }
  
      //add filter by search
      if (isset($filter->search)) {
        //find search in columns
        $query->where(function ($query) use ($filter) {
          $query->where('id', 'like', '%' . $filter->search . '%')
            ->orWhere('updated_at', 'like', '%' . $filter->search . '%')
            ->orWhere('created_at', 'like', '%' . $filter->search . '%');
        });
      }
      
      
    }
    
    /*== FIELDS ==*/
    if (isset($params->fields) && count($params->fields))
      $query->select($params->fields);
  
   // $this->validateIndexAllPermission($query, $params);
    
    /*== REQUEST ==*/
    if (isset($params->page) && $params->page) {
      return $query->paginate($params->take);
    } else {
      $params->take ? $query->take($params->take) : false;//Take
      return $query->get();
    }
  }
  
  
  public function getItem($criteria, $params = false)
  {
    //Initialize query
    $query = $this->model->query();
    
    /*== RELATIONSHIPS ==*/
    if (in_array('*', $params->include ?? [])) {//If Request all relationships
      $query->with([]);
    } else {//Especific relationships
      $includeDefault = [];//Default relationships
      if (isset($params->include))//merge relations with default relationships
        $includeDefault = array_merge($includeDefault, $params->include ?? []);
      $query->with($includeDefault);//Add Relationships to query
    }
    
    /*== FILTER ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;
  
  
      //by related id
      if (isset($filter->requestableId)) {
        $query->where('requestable_id', $filter->requestableId);
      }
      //by related name
      if (isset($filter->requestableType)) {
        $query->where('requestable_type', $filter->requestableType);
      }
      
      //by category
      if (isset($filter->categoryId)) {
        $query->where('category_id', $filter->categoryId);
      }
      
      //by type
      if(isset($filter->type) && $filter->type) {
        if(!is_array($filter->type)) $filter->type = [$filter->type];
        $query->whereIn("type",$filter->type);
      }
      
      if (isset($filter->createdBy)) {
        $query->where('created_by', $filter->createdBy);
      }
      
      if (isset($filter->field))//Filter by specific field
        $field = $filter->field;
    }
    
    /*== FIELDS ==*/
    if (isset($params->fields) && count($params->fields))
      $query->select($params->fields);
    
    /*== REQUEST ==*/
    return $query->where($field ?? 'id', $criteria)->first();
  }
  
  
  public function create($data)
  {

    $model = $this->findByAttributes([
      "type" => $data["type"],
      "requestable_id" => $data["requestable_id"] ?? null,
      "requestable_type" => $data["requestable_type"] ?? null,
      "created_by" => $data["created_by"] ?? \Auth::id() ?? null,
    ]);

    if(!$model)
      return $this->model->create($data);
    else
      return $model;
  }
  
  
  public function updateBy($criteria, $data, $params = false)
  {
    /*== initialize query ==*/
    $query = $this->model->query();
    
    /*== FILTER ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;
      
      //Update by field
      if (isset($filter->field))
        $field = $filter->field;
    }
    /*== REQUEST ==*/
    $model = $query->where($field ?? 'id', $criteria)->first();
    if($model){
      
      $oldData = $model->toArray();
      $model->update((array)$data);
      
      return $model;
    }else{
      return false;
    }
  }
  
  
  public function deleteBy($criteria, $params = false)
  {
    /*== initialize query ==*/
    $query = $this->model->query();
    
    /*== FILTER ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;
      
      if (isset($filter->field))//Where field
        $field = $filter->field;
    }
    
    /*== REQUEST ==*/
    $model = $query->where($field ?? 'id', $criteria)->first();
    if(isset($model->id)){
      $permission = $params->permissions['requestable.requestables.destroy'] ?? false;
  
      // solo se permite borrar request si:
      // se tiene el permiso para eliminar requests
      // o que el request haya sido creado por el user que está autenticado
      if ($permission || \Auth::id() == $model->created_by) {
    
        //call Method delete
        $model->delete();
    
      } else {
        throw new \Exception('Permission denied', 401);
      }
    }
    
  }
  
  function validateIndexAllPermission(&$query, $params)
  {
    // filter by permission: index all leads
    
    if (!isset($params->permissions['requestable.requestables.index-all']) ||
      (isset($params->permissions['requestable.requestables.index-all']) &&
        !$params->permissions['requestable.requestables.index-all'])) {
      $user = $params->user;
      
      $query->where('created_by', $user->id);
      
      
    }
  }
  
  public function moduleConfigs(){
  
    $module = app('modules');
    $enabledModules = $module->allEnabled();

    if (is_string($enabledModules)) {
      return config('asgard.' . strtolower($enabledModules) . ".config.requestable");
    }
  
    $modulesWithConfigs = [];
    foreach ($enabledModules as $module) {
      if ($moduleConfigs = config('asgard.' . strtolower($module->getName()) . ".config.requestable")) {
        $modulesWithConfigs = array_merge($modulesWithConfigs, $moduleConfigs);
      }
    }
    
    return $modulesWithConfigs;
    
  }
}
