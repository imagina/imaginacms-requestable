<?php

use Illuminate\Routing\Router;

/** @var Router $router */
$router->group(['prefix' => '/requestable/v1'], function (Router $router) {
  
  $router->apiCrud([
    'module' => 'requestable',
    'prefix' => 'categories',
    'controller' => 'CategoryApiController',
    'middleware' => [
      'index' => [],
      'show' => [],
      'create' => ['auth:api','auth-can:requestable.categories.create'],
      'update' => ['auth:api','auth-can:requestable.categories.edit']
    ]
  ]);
  
  $router->apiCrud([
    'module' => 'requestable',
    'prefix' => 'statuses',
    'controller' => 'StatusApiController',
    'middleware' => [
    
    ]
  ]);
  
  $router->apiCrud([
    'module' => 'requestable',
    'prefix' => 'requestables',
    'controller' => 'RequestableApiController',
    'middleware' => [
      'index' => ['auth:api','auth-can:requestable.requestables.index'],
      'show' => ['auth:api','auth-can:requestable.requestables.index'],
      'create' => ['auth:api','auth-can:requestable.requestables.create'],
      'update' => ['auth:api','auth-can:requestable.requestables.edit']

    ]
  ]);

  $router->apiCrud([
    'module' => 'requestable',
    'prefix' => 'category-rule',
    'controller' => 'CategoryRuleApiController',
    'middleware' => [
      'update' => ['auth:api','auth-can:requestable.categoryrules.edit']
    ]
  ]);
  

  
  
  //======  REQUESTS
  require('ApiRoutes/requestablesRoutes.php');

  require('ApiRoutes/statusesRoutes.php');

});
