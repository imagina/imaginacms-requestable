<?php
 
namespace Modules\Requestable\Jobs;
 
 use Illuminate\Bus\Queueable;

 use Illuminate\Contracts\Queue\ShouldQueue;
 use Illuminate\Foundation\Bus\Dispatchable;
 use Illuminate\Queue\InteractsWithQueue;
 use Illuminate\Queue\SerializesModels;

 use Modules\Requestable\Entities\Requestable;

 use Modules\User\Entities\Sentinel\User;
 
class ProcessNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
 

    private $rule;
    private $requestable;
    private $lastHistoryStatusId;

    public $notificationService;
    public $commentService;
    public $userApiRepository;

    private $log = "Requestable: Jobs|ProcessNotification|";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($rule,$requestable,$lastHistoryStatusId)
    {
        
        $this->rule = $rule;
        $this->requestable = $requestable;
        $this->lastHistoryStatusId = $lastHistoryStatusId;

        $this->notificationService = app("Modules\Notification\Services\Inotification");
        $this->commentService = app("Modules\Icomments\Services\CommentService");
        $this->userApiRepository = app("Modules\Iprofile\Repositories\UserApiRepository");
    }

    /**
     * Handle init
    */
    public function handle()
    {

        $catRule = $this->rule->categoryRule;
        \Log::info($this->log.'AutomationRuleID:'.$this->rule->id.' Category:'.$catRule->system_name.' ============');

        $ruleFields = count($this->rule->fields)>0 ? $this->rule->fields : null;
        //\Log::info('Requestable: Jobs|ProcessNotification|RuleFields: '.$ruleFields);

        //Requestable Old
        $requestableData = $this->requestable;
        //Requestable Now
        $requestableNow = Requestable::find($requestableData->id);

        //There may be saved jobs but the current requestable does not have the same status
        //There may be saved jobs with the same status but not with the same information
        //If the status history ids are the same then continue..
        if($requestableNow->lastStatusHistoryId()==$this->lastHistoryStatusId){
          
            // Only if automation rule has fields (from fillables)
            if(!is_null($ruleFields)){

                $requestableFields = count($requestableData->fields)>0 ? $requestableData->fields : null;
                //\Log::info('Requestable: Jobs|ProcessNotification|RequestableFields: '.$requestableFields);

                // Only if requestable has fields (from fillables)
                if(!is_null($requestableFields)){

                    //Params to execute notifications
                    $params = [
                        "ruleFields" => $ruleFields,
                        "requestableFields" => $requestableFields,
                        "toFieldName" => $this->rule->to,
                        "requestableData" => $requestableData,
                        "rule" => $this->rule,
                        "catRule" => $catRule
                    ];

                    //Separate so that the next ifs are shorter
                    $sepCatRule = explode("-",$catRule->system_name);

                    // Depending on the category - Execute notifications
                    if($sepCatRule[1]=="email")
                        $this->sendEmail($params);
                    
                    if($sepCatRule[1]=="whatsapp")
                        $this->sendMobile($params,"whatsapp"); 
                    
                    if($sepCatRule[1]=="sms")
                        $this->sendMobile($params,"sms");
                    
                    if($sepCatRule[1]=="telegram")
                        $this->sendMobile($params,"telegram");

                }

            }else{
                \Log::info($this->log.'AutomationRuleID:'.$this->rule->id.' - Not fields (fillables) for this Rule ');
            }

        }else{
            \Log::info($this->log.'Not same ids from Status History');
        }

        
    }

    /**
    * Notification Send Email
    * @param $ruleFields
    * @param $requestableFields
    * @param $toFieldName (from attribute 'to' saved in the Automation Rule )
    */
    public function sendEmail(array $params)
    {
        
        // Fillables from AutomationRule
        $from = $this->getValueField('from',$params['ruleFields']) ?? null;
        $subject = $this->getValueField('subject',$params['ruleFields']) ?? "Subject Test";
        $message = $this->getValueField('message',$params['ruleFields']) ?? "Message Test";
        \Log::info($this->log.'sendEmail|FROM: '.$from.' - SUBJECT: '.$subject.' - MESSAGE: '.$message);

        $resultEmails = $this->getEmailsByCategoryRule($params);
       
        if(is_array($resultEmails)) 
            $emailsTo = $resultEmails;
        else
            $emailsTo[] = $resultEmails;
       
        \Log::info($this->log.'sendEmail|emailsTo: '.json_encode($emailsTo));
    
        if(!empty($emailsTo) && !is_null($emailsTo[0])){

            //Check Variables to replace
            $subject = $this->checkVariables($subject,$params['requestableFields']);
            $message = $this->checkVariables($message,$params['requestableFields']);

            //Save a comment
            $this->saveComment("email",$params['requestableData'],$message,$from,$emailsTo);
            
            if(config("app.env")=="production"){
                
                $this->notificationService->to([
                    "email" => $emailsTo
                ])->push([
                    "title" => $subject,
                    "message" => $message,
                    "fromAddress" => $from,
                    "fromName" => "",
                    "setting" => [
                        "saveInDatabase" => true
                    ]
                ]);

            }else{
                \Log::info($this->log.'Email not sent (app.env is not Production)');
            }

        }
                
    }


    /**
    * Notification Send Mobile
    * @param $ruleFields
    * @param $requestableFields
    * @param $toFieldName (from attribute 'to' saved in the Automation Rule )
    * @param $type (whatsapp,telegram,sms)
    */
    public function sendMobile(array $params,string $type)
    {

        // Fillables from AutomationRule
        $message = $this->getValueField('message',$params['ruleFields']) ?? "Message Test";
        \Log::info($this->log.'sendMobile|message: '.$message);
           
        $sendTo = $this->getPhonesByCategoryRule($params);
        \Log::info($this->log.'sendMobile|sendTo: '.json_encode($sendTo));

        if(!is_null($sendTo)){

            //Check Variables to replace
            $message = $this->checkVariables($message,$params['requestableFields']);

            $messageToSend = [
                "message" => $message,
                "provider" => $type,
                //"recipient_id" => $sendTo,
                "sender_id" => $params['requestableData']->requestedBy->id,
                "send_to_provider" => true,
                "type" => "template"
            ];
            //\Log::info('Requestable: Jobs|ProcessNotification|sendMobile|messageToSend: '.json_encode($messageToSend));

            //Save a comment
            $this->saveComment($type,$params['requestableData'],$message,null,$sendTo);

            if(config("app.env")=="production"){
                //Message service from Ichat Module
                if (is_module_enabled('Ichat')) {

                    $messageService = app("Modules\Ichat\Services\MessageService");

                    foreach ($sendTo as $key => $phone) {
                        $messageToSend['recipient_id'] = $phone;
                        $messageService->create($messageToSend);
                    }

                }else{

                    foreach ($sendTo as $key => $phone) {
                        $this->notificationService->provider($type)
                            ->to($phone)
                        ->push([
                            "type" => "template",
                            "message" => $message
                        ]);
                    }
                }
            }else{
                \Log::info($this->log.'Notification not sent (app.env is not Production)');
            }
            
        }
        
    }

    
    /**
    * Get Value from specific field from Fillables Fields
    */
    public function getValueField(string $name, object $fields)
    {

        $value = null;
        $valueInforField = $fields->firstWhere('name',$name);

        //\Log::info('Requestable: Jobs|ProcessNotification|getValueField|value: '.$valueInforField);

        if(!is_null($valueInforField) && !empty($valueInforField))
            $value = $valueInforField->translations->first()->value;
    
        return $value;

    }

    /**
    * @param $str (text which can contain variables)
    */
    public function checkVariables($str,$requestableFields){
        
        if (preg_match_all("~\{\{\s*(.*?)\s*\}\}~", $str, $matches)){
            \Log::info('Requestable: Jobs|ProcessNotification|checkVariables|Matches: '.json_encode($matches[1]));
            
            foreach ($matches[1] as $key => $match) {
                //\Log::info('Requestable: CheckVariables|Search this value: '.$match);

                $value = $this->getValueField($match,$requestableFields) ?? "--";
                //\Log::info('Requestable: CheckVariables|Value: '.$value);
                
                if(is_array($value))
                    $value = implode(",",$value);

                //Ready to replace
                $find = "{{".$match."}}";
                $str = str_replace($find,$value,$str);

            }

            //\Log::info('Requestable: Jobs|ProcessNotification|checkVariables|Str: '.$str);
        }
        
        return $str;
    }

    /**
     * @param $type (Notification type)
     */
    public function saveComment($type,$model,$message,$from=null,$to=null)
    {

        $comment = trans('requestable::common.notifications.title sent');
        $comment = $comment." <b>".$type."</b>";
       
        if(!is_null($from) && !empty($from))
            $comment = $comment." ".trans('requestable::common.notifications.from')." <b>".$from."</b>";

        if(!is_null($to)){
            if(is_array($to))
                $to = implode(" ",$to);
            
            $comment = $comment." ".trans('requestable::common.notifications.to')." <b>".$to."</b>";
        }

        $comment = $comment."<p>".$message."</p>";

        $this->commentService->create($model,[
                "user_id" => $model->updated_by, // Needed because is a job
                "comment" => $comment,
                "internal" => true,
                "type" => "notification"
            ]
        );

    }

     /**
     * Process to get emails depending on the category of the rule
     */
    public function getEmailsByCategoryRule($params)
    {

        //\Log::info('Requestable: Jobs|ProcessNotification|getEmailsByCategoryRule');

        //Email from: Requestable Data - Requested By 
        if($params['catRule']->system_name=="send-email-to-requested-by" ){
            $emailsTo = $params['requestableData']->requestedBy->email;
        }

        //Email from: Automation Rule Data -  To (Field with users Ids)
        if($params['catRule']->system_name=="send-email-to-employee" ){
            
            $usersIds = $params['toFieldName'];
            $paramsRequest = ['filter' => ['userId' => $usersIds]];
            $users = $this->userApiRepository->getItemsBy(json_decode(json_encode($paramsRequest)));
            
            $emailsTo = null;
            if(!is_null($users)){
                $plucked = $users->pluck('email');
                $emailsTo = $plucked->all();
            }

        }
        
        //Email from: Fillables from Requestable - (Like the first Version)
        if($params['catRule']->system_name=="send-email-to-form-field" ){
            $emailsTo = $this->getValueField($params['toFieldName'][0],$params['requestableFields']);
        }

        //Emails from:  Automation Rule Data -  To (Field with the data)
        if($params['catRule']->system_name=="send-email-to-external-data" ){
            $onlyEmailValues = array_column($params['toFieldName'],'email');
            $emailsTo = $onlyEmailValues;
        }

        return $emailsTo;

    }

    /*
    * Process to get phones depending of the category rule
    */
    public function getPhonesByCategoryRule($params)
    {

        //\Log::info($this->log.'getPhonesByCategoryRule');

        //Separate so that the next ifs are shorter
        $sepCatRule = explode("-",$params['catRule']->system_name);

       //Phone from: Requestable Data - Requested By 
        if($sepCatRule[3]=="requested"){
            $sendTo[] = $this->getUserPhone($params['requestableData']->requestedBy);
        }   
       
        //Phone from: Automation Rule Data -  To (Contains Field with users Ids)
        if($sepCatRule[3]=="employee"){
           
            $usersIds = $params['toFieldName'];
            $params = ['filter' => ['userId' => $usersIds]];
            $users = $this->userApiRepository->getItemsBy(json_decode(json_encode($params)));
            
            $sendTo = [];
            foreach ($users as $key => $user) {
                $userPhone = $this->getUserPhone($user);
                if(!is_null($userPhone))
                    array_push($sendTo,$userPhone);
            }

        }

        //Phone from: Fillables from Requestable - (Like the first Version)
        if($sepCatRule[3]=="form"){
           $sendTo[] = $this->getValueField($params['toFieldName'][0],$params['requestableFields']);
        }

        //Phone from:  Automation Rule Data -  To (Contains field with the data)
        if($sepCatRule[3]=="external"){
            $onlyPhoneValues = array_column($params['toFieldName'],'phoneNumber');
            $sendTo = $onlyPhoneValues;
        }

        return $sendTo;

    }

    /**
     * Get User phone from profile fields
     */
    public function getUserPhone($user)
    {   
       
        $phone = null;

        $fieldPhone = $user->fields->where("name","cellularPhone");

        if(count($fieldPhone)>0) {
            $phone = $fieldPhone[0]->value; 
        }

        return $phone;
    }


}