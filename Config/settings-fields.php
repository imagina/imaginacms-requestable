<?php

return [
  'showExtraFieldsFromFormInReport' => [
    'value' => "0",
    'name' => 'requestable::showExtraFieldsFromFormInReport',
    'type' => 'checkbox',
    'props' => [
      'label' => 'requestable::settings.showExtraFieldsFromFormInReport',
      'trueValue' => "1",
      'falseValue' => "0",
    ]
  ],
  //Default contact role
  'defaultContactRole' => [
    "onlySuperAdmin" => true,
    'name' => 'requestable::defaultContactRole',
    'value' => null,
    'type' => 'select',
    'props' => [
      'label' => 'requestable::settings.defaultContactRole',
      
    ],
    'loadOptions' => [
      'apiRoute' => 'apiRoutes.quser.roles',
      'select' => ['label' => 'name', 'id' => 'id']
    ]
  ],
];
