<?php

namespace Modules\Requestable\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CreateFormTableSeeder extends Seeder
{

  /**
   * Run the database seeds.
   *
   * @return void
  */
  public function run(){
    
    Model::unguard();

    $formService = app("Modules\Requestable\Services\FormService");
    $systemName = "lead";
    try{

      $form = $formService->create($systemName);
      if(!is_null($form)){
        $this->createCategoryAndStatusesFromConfig($form);
      }
       
                  
    }catch(\Exception $e){
      \Log::error('Requestable: Seeders|CreateForm|Message: '.$e->getMessage());
          
    }  

  }

  /*
  * Create Category and Status
  */  
  public function createCategoryAndStatusesFromConfig($form){

    $config = config('asgard.requestable.config.requestable-leads');

    $config['formId'] = $form->id;

    // Call requestable
    $requestableService = app("Modules\Requestable\Services\RequestableService");
    
    $requestableService->createFromConfig($config);
                  
  }

  

}
