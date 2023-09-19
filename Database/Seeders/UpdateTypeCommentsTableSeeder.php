<?php

namespace Modules\Requestable\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class UpdateTypeCommentsTableSeeder extends Seeder
{

  /**
  *Seeder to update old comments to the New Comments Update
  */
  public function run(){
    
    Model::unguard();

    try{

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
