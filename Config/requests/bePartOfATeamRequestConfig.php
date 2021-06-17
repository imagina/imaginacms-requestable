<?php

return [
  "type" => "bePartOfaTeamRequest",
  "title" => "Be part of a team Request",
  
  'defaultStatus' => 0,

  "events" => [
    "create" => "Modules\\Iteam\\Events\\BePartOfATeamRequestWasSent",
    //"update" => "Modules\\Iteam\\Events\\BePartOfATeamRequestWasSent",
    //"delete" => "Modules\\Fhia\\Events\\InspectionRequestWasDeleted",
  ],
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
];