<?php

namespace Modules\Requestable\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class UpdateTypeCommentsTableSeeder extends Seeder
{

  /**
  *
  */
  public function run(){
    
    Model::unguard();

    try{

      //OJO Derrepente agregar o no una validacion para q se ejecute este Seeder

      $commentService = app("Modules\Icomments\Services\CommentService"); 

      $commentableType = "Modules\Requestable\Entities\Requestable";
      $dataToSearch = [
        ['type' => 'statusChanged', 'text' => 'Estado Actualizado' ],
        ['type' => 'notification', 'text' => 'Notificacion enviada' ],
      ];

      $commentService->updateTypeByText($dataToSearch, $commentableType);     

    }catch(\Exception $e){
      \Log::error('Requestable: Seeder|UpdateTypeCommentsTableSeeder|Message: '.$e->getMessage());
      dd($e);
    }

  }

}
