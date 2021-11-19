<?php

namespace Modules\Requestable\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Requestable\Entities\DefaultStatus;
use Modules\Iforms\Events\SyncFormeable;

class RequestableTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Model::unguard();
    
    $requestableRepository = app("Modules\Requestable\Repositories\RequestableRepository");
    $categoryRepository = app("Modules\Requestable\Repositories\CategoryRepository");
    $statusRepository = app("Modules\Requestable\Repositories\StatusRepository");
    $locale = \LaravelLocalization::setLocale() ?: \App::getLocale();
    $params = [
      "filter" => [
        "field" => "type",
      ],
      "include" => [],
      "fields" => [],
    ];
    $requestableConfig = $requestableRepository->moduleConfigs();
    
    foreach ($requestableConfig as $config) {
      $params = [
        "filter" => [
          "field" => "type",
        ],
        "include" => [],
        "fields" => [],
      ];
      
      if (isset($config["type"])) {
        
        $category = $categoryRepository->getItem($config["type"] ?? '', json_decode(json_encode($params)));
        
        if (!isset($category->id)) {
          $category = $categoryRepository->create([
            'type' => $config["type"],
            'time_elapsed_to_cancel' => $config["timeElapsedToCancel"] ?? -1,
            'events' => $config["events"] ?? null,
            'requestable_type' => $config["requestableType"],
            $locale => [
              "title" => trans($config["title"])
            ]
          ]);
          
          if(isset($config["formId"]) && !empty($config["formId"]))
            event(new SyncFormeable($category, ["form_id" => setting($config["formId"], null, null)]));
          
          if (isset($config["useDefaultStatuses"]) && $config["useDefaultStatuses"]) {
            $statuses = (new DefaultStatus())->lists();
          } else {
            $statuses = $config["statuses"];
          }
          
          foreach ($statuses as $key => $status) {
       
            $statusRepository->create([
                "category_id" => $category->id,
                'value' => $key,
                'final' => $status["final"] ?? false,
                'default' => $status["default"] ?? false,
                'cancelled_elapsed_time' => $config["statusToSetWhenElapsedTime"] ?? false,
                'events' => $config["statusEvents"][$key] ?? null,
                'delete_request' => $config["deleteWhenStatus"][$key] ?? false,
                $locale => [
                  "title" => trans($status["title"])
                ]
              ]
            );
          }
        }
      }
      
    }
  }
}
