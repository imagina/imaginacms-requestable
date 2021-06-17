<?php

return [
  'name' => 'Requestable',

  /*
   * 0 Pending
   * 1 accepted
   * 2 rejected
   * 3 cancelled
   * */
  'defaultStatus' => 0,//Pending
  
  'deleteWhenStatus' => [
    0 => false,
    1 => true,
    2 => false,
    3 => true
  ],
  
  "requests" => [
  
    [
      "type" => "joinToTeamRequest",
      "title" => "Join to Team Request",
    
      'defaultStatus' => 0,
    
      "events" => [
        "create" => "Modules\\Iteam\\Events\\JoinToTeamRequestWasSent",
        //"update" => "Modules\\Iteam\\Events\\JoinToTeamRequestWasUpdated",
        //"delete" => "Modules\\Fhia\\Events\\InspectionRequestWasDeleted",
      ],
  
      "statusEvents" => [
        1 => "Modules\\Iteam\\Events\\JoinToTeamRequestWasAccepted",
      ],
  
     // "etaEvent" => "Modules\\Iteam\\Events\\JoinToTeamRequestWasAccepted",
      
      "rquestableId" => [
        'value' => '',
        "label" => "Team",
        'type' => 'number',
        'loadEntity' => [
          'apiRoute' => 'api.iteam.teams',
        ]
      ],
      "requestableType" => "Modules\\Iteam\\Entities\\Team",
    
      "forms" => [
        "main" => [
          "name" => "main",
          "label" => "General Info",
          "fields" => [
          
          
            "cityId" => [
              'value' => null,
              'type' => 'select',
              'props' => [
                'label' => 'City',
                'clearable' => true,
              ],
              'loadOptions' => [
                "filter" => [
                  ""
                ],
                'apiRoute' => 'api.location.cities',
                'select' => ['label' => 'name', 'id' => 'id']
              ],
            ],
            'inspectionDate' => [
              'value' => null,
              'type' => 'datetime'
            ],
            'fieldManagerName' => [
              'value' => '',
              'type' => 'text',
              "editable" => false,
          
            ],
            'inspectionDate' => [
              'value' => null,
              'type' => 'datetime'
            ],
          ]
        ],
        "parts" => [
          "name" => "parts",
          "label" => "Parts",
          "multiple" => true
        ]
    
      ]
    ]
  ],
  
  "notifiable" => [
    [
      "title" => "Requestable",
      "entityName" => "Modules\\Requestable\\Entities\\Requestable",
      "events" => [
        [
          "title" => "Join to Team Request",
          "path" => "Modules\\Iteam\\Events\\JoinToTeamRequestWasSent"
        ],
      ],
      "conditions" => [
    
      ],
      "settings" => [
        "email" => [
          "recipients" => [
          ]
        ],
        "sms" => [
        ],
        "pusher" => [
        ],
        "firebase" => [
        ],
      ],
    ]
  ]
];
