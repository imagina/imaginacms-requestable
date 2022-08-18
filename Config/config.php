<?php

return [
  'name' => 'Requestable',

  /*
  |--------------------------------------------------------------------------
  | Define all the exportable available
  |--------------------------------------------------------------------------
  */
  'exportable' => [
    "requestables" => [
      'moduleName' => "Requestable",
      'fileName' => "Requests",
      'exportName' => "RequestablesExport"
    ]
  ],

  /*
  |--------------------------------------------------------------------------
  | Same params Readme - Requestable - Only execute by CreateForm Seeder
  |--------------------------------------------------------------------------
  */
  "requestable-leads" => [
    
      //Required: this is the identificator of the request, must be unique with respect to other requestable types 
      "type" => "leads",
     
      // Title can be trantaled or not, the language take the config app.locale 
      "title" => "requestable::categories.leads.title",
           
      // Optional: This columns has true by default
      "internal" => false,
        
      
      //Optional: if the useDefaultStatuses is true, statuses is ignored 
      "statuses" => [
        1 => [
            "id" => 1,
            "title" => "requestable::statuses.leads.new", // Title can be trantaled or not, the language take the config app.locale 
            "default" => true
        ],
        2 => [
            "id" => 2,
            "title" => "requestable::statuses.leads.contacted"
        ],
        3 => [
            "id" => 3,
            "title" => "requestable::statuses.leads.commercial proposal"
        ],
        4 => [
            "id" => 4,
            "title" => "requestable::statuses.leads.in progress"
        ],
        5 => [
            "id" => 5,
            "title" => "requestable::statuses.leads.successful",
            "final" => true
        ],
        6 => [
            "id" => 6,
            "title" => "requestable::statuses.leads.lost",
            "final" => true
        ],
      ]
      
  ]
  

];
