<?php

use Illuminate\Routing\Router;

/** @var Router $router */
$router->group(['prefix' => '/requestable/v1'], function (Router $router) {
  
  
  //======  REQUESTS
  require('ApiRoutes/requestablesRoutes.php');
});
