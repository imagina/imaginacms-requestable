<?php

namespace Modules\Requestable\Events\Handlers;

class CheckResponsibleRequestable
{
    private $log = "Requestable: Handler|CheckResponsibleRequestable|";
    private $commentService;

    public function __construct()
    {
        $this->commentService = app("Modules\Icomments\Services\CommentService");
    }

    /**
     * Init handle
    */
    public function handle($event){

        try {

            //Model
            $requestable = $event->requestable;
            //\Log::info($this->log."ID: ".$requestable->id);
        
            if(get_class($event)=="Modules\Requestable\Events\RequestableWasUpdated"){
                
                if($requestable->wasChanged("responsible_id")){
                    //\Log::info($this->log."createComment");
                    $this->createComment($requestable); 
                }
                
            }

        } catch (\Exception $e) {
            \Log::error($this->log.'Message: ' . $e->getMessage() . ' | FILE: ' . $e->getFile() . ' | LINE: ' . $e->getLine());
        }

        
    }// If handle


    private function createComment($model)
    {
        
        $comment = trans('requestable::requestables.responsible.updated',['responsible' => $model->responsible->present()->fullname]);

        $this->commentService->create($model,[
                "comment" => $comment,
                "internal" => true,
                "type" => "responsibleChanged"
            ]
        );

    }
   
}