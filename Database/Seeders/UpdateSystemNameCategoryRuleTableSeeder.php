<?php

namespace Modules\Requestable\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Modules\Requestable\Repositories\CategoryRuleRepository;

class UpdateSystemNameCategoryRuleTableSeeder extends Seeder
{

  private $categoryRuleRepository;


  public function __construct(
    CategoryRuleRepository $categoryRuleRepository
  ){
      $this->categoryRuleRepository = $categoryRuleRepository;
  }

  /**
  *Seeder to update system names to old category rules
  */
  public function run(){
    
    Model::unguard();

    try{

      // Old system names with new system names
      $systemNames = [
        'client-communications'=> 'main-form-comunication',
        'send-email' => 'send-email-to-form-field',
        'send-sms' => 'send-sms-to-form-field',
        'send-telegram' => 'send-telegram-to-form-field',
        'send-whatsapp' => 'send-whatsapp-to-form-field',
      ];

      foreach($systemNames as $key => $value) {

        $existCategory = $this->findCategory($key);

        if(!is_null($existCategory)){
          $dataModel = ['system_name' => $value];
          $categoryUpdated = $this->categoryRuleRepository->updateBy($existCategory->id, $dataModel);
        }
      }

    }catch(\Exception $e){
      \Log::error('Requestable: Seeder|UpdateSystemNameCategoryRulesTableSeeder|Message: '.$e->getMessage());
      dd($e);
    }

  }

  /*
  * Find category
  */
  public function findCategory($systemName){

    $params = [
      "filter" => ["field" => "system_name"],
      "include" => [],
      "fields" => [],
    ];

    $category = $this->categoryRuleRepository->getItem($systemName, json_decode(json_encode($params)));

    return $category;
  }

}
