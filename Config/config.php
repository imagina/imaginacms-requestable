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
      'exportName' => "RequestablesExport",
      'exportFields' => [
        //Choose report Type
        'reportType' => [
          'value' => "detailed",
          'name' => 'isite::reportType',
          'type' => 'select',
          'colClass' => 'col-6',
          'props' => [
            'label' => 'requestable::exports.exportFields.report type',
            'useInput' => false,
            'useChips' => false,
            'multiple' => false,
            'hideDropdownIcon' => true,
            'newValueMode' => 'add-unique',
            'options' => [
              ['label' => 'requestable::exports.exportFields.type.detailed', 'value' => "detailed"],
              ['label' => 'requestable::exports.exportFields.type.statuses', 'value' => "statuses"],
            ]
          ]
        ],
      ]
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
        "type" => 0, //In Progress
        "color" => "#bf5454",
        "default" => true
      ],
      2 => [
        "id" => 2,
        "title" => "requestable::statuses.leads.contacted",
        "type" => 0, //In Progress
        "color" => "#6d2080"
      ],
      3 => [
        "id" => 3,
        "title" => "requestable::statuses.leads.commercial proposal",
        "type" => 0, //In Progress
        "color" => "#38d3b2"
      ],
      4 => [
        "id" => 4,
        "title" => "requestable::statuses.leads.in progress",
        "type" => 0, //In Progress
        "color" => "#ec7f17"
      ],
      5 => [
        "id" => 5,
        "title" => "requestable::statuses.leads.successful",
        "type" => 2, //Success
        "color" => "#2cc03d",
        "final" => true
      ],
      6 => [
        "id" => 6,
        "title" => "requestable::statuses.leads.lost",
        "type" => 1, //Failed
        "color" => "#e34b4b",
        "final" => true
      ],
    ]

  ],

  /*
  |--------------------------------------------------------------------------
  | Categories Rule to Seeder
  |--------------------------------------------------------------------------
  */
  "categories-rules" => [
    
    //--------------------------------------------------------------------------
    //PARENT - internal-communication 
    //--------------------------------------------------------------------------
    'internal-communication' => [
      'systemName' => 'internal-communication',
      'en' => ['title' => 'Internal Comunication'],
      'es' => ['title' => 'Comunicacion Interna'],
    ],
    
    //PARENT - Child Category - employee-alert
    'employee-alert' => [
      'systemName' => 'employee-alert',
      'en' => ['title' => 'Employee alert'],
      'es' => ['title' => 'Alerta a empleados'],
      'parentSystemName' => 'internal-communication', //from parent
    ],
    //Child Category - send-email-to-employee
    'send-email-to-employee' => [
      'systemName' => 'send-email-to-employee',
      'parentSystemName' => 'employee-alert', //from parent
      'en' => ['title' => 'Send Email'],
      'es' => ['title' => 'Enviar email'],
      'formFields' => [
        'from' => [
          'value' => null,
          'name' => 'from',
          'type' => 'select',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.quser.users',
            'filterByQuery' => true,
            'select' => ['label' => 'email', 'value' => 'email', 'id' => 'email']
          ],
          'props' => [
            'label' => 'requestable::common.formFields.from',
            'multiple' => false,
            'clearable' => true,
          ],
        ],
        'subject' => [
          'value' => null,
          'name' => 'subject',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.subject'
          ]
        ],
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ],
      ]
    ],
    //Child Category - send-sms-to-employee
    'send-sms-to-employee' => [
      'systemName' => 'send-sms-to-employee',
      'parentSystemName' => 'employee-alert', //from parent
      'en' => ['title' => 'Send SMS'],
      'es' => ['title' => 'Enviar sms'],
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],
    //Child Category - send-telegram-to-employee
    'send-telegram-to-employee' => [
      'systemName' => 'send-telegram-to-employee',
      'parentSystemName' => 'employee-alert', //from parent
      'en' => ['title' => 'Send telegram'],
      'es' => ['title' => 'Enviar telegram'],
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'isTranslatable' => true,
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],
    //Child Category - send-whatsapp-to-employee
    'send-whatsapp-to-employee' => [
      'systemName' => 'send-whatsapp-to-employee',
      'parentSystemName' => 'employee-alert', //from parent
      'en' => ['title' => 'Send whatsapp'],
      'es' => ['title' => 'Enviar whatsapp'],
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'isTranslatable' => true,
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],

    //--------------------------------------------------------------------------
    //PARENT - external-communication
    //--------------------------------------------------------------------------
    'external-communication' => [
      'systemName' => 'external-communication',
      'en' => ['title' => 'External Comunication'],
      'es' => ['title' => 'Comunicacion Externa'],
    ],

    //PARENT - Child Category - main-form-comunication
    'main-form-comunication' => [
      'systemName' => 'main-form-comunication',
      'en' => ['title' => 'Main Form Communication'],
      'es' => ['title' => 'Comunicacion con el Formulario Principal'],
      'parentSystemName' => 'external-communication', //from parent
    ],
    //Child Category - send-email-to-form-field
    'send-email-to-form-field' => [
      'systemName' => 'send-email-to-form-field',
      'parentSystemName' => 'main-form-comunication', //from parent
      'en' => ['title' => 'Send Email'],
      'es' => ['title' => 'Enviar email'],
      'options' => ['filterFormFieldType' => 'email'],//type field iform
      'formFields' => [
        'from' => [
          'value' => null,
          'name' => 'from',
          'type' => 'select',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.quser.users',
            'filterByQuery' => true,
            'select' => ['label' => 'email', 'value' => 'email', 'id' => 'email']
          ],
          'props' => [
            'label' => 'requestable::common.formFields.from',
            'multiple' => false,
            'clearable' => true,
          ],
        ],
        'subject' => [
          'value' => null,
          'name' => 'subject',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.subject'
          ]
        ],
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ],
      ]
    ],
    //Child Category - send-sms-to-form-field
    'send-sms-to-form-field' => [
      'systemName' => 'send-sms-to-form-field',
      'parentSystemName' => 'main-form-comunication', //from parent
      'en' => ['title' => 'Send SMS'],
      'es' => ['title' => 'Enviar sms'],
      'options' => ['filterFormFieldType' => 'phone'],//type field iform
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],
    //Child Category - send-telegram-to-form-field
    'send-telegram-to-form-field' => [
      'systemName' => 'send-telegram-to-form-field',
      'parentSystemName' => 'main-form-comunication', //from parent
      'en' => ['title' => 'Send telegram'],
      'es' => ['title' => 'Enviar telegram'],
      'options' => ['filterFormFieldType' => 'phone'],//type field iform
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'isTranslatable' => true,
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],
    //Child Category - send-whatsapp-to-form-field
    'send-whatsapp-to-form-field' => [
      'systemName' => 'send-whatsapp-to-form-field',
      'parentSystemName' => 'main-form-comunication', //from parent
      'en' => ['title' => 'Send whatsapp'],
      'es' => ['title' => 'Enviar whatsapp'],
      'options' => ['filterFormFieldType' => 'phone'],//type field iform
      'status' => 0,
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'isTranslatable' => true,
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],

    //PARENT - Child Category - external-data-comunication
    'external-data-comunication' => [
      'systemName' => 'external-data-comunication',
      'en' => ['title' => 'External data comunication'],
      'es' => ['title' => 'Comunicacion con datos externos'],
      'parentSystemName' => 'external-communication', //from parent
    ],
    //Child Category - send-email-to-external-data
    'send-email-to-external-data' => [
      'systemName' => 'send-email-to-external-data',
      'parentSystemName' => 'external-data-comunication', //from parent
      'en' => ['title' => 'Send Email'],
      'es' => ['title' => 'Enviar email'],
      'formFields' => [
        'from' => [
          'value' => null,
          'name' => 'from',
          'type' => 'select',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.quser.users',
            'filterByQuery' => true,
            'select' => ['label' => 'email', 'value' => 'email', 'id' => 'email']
          ],
          'props' => [
            'label' => 'requestable::common.formFields.from',
            'multiple' => false,
            'clearable' => true,
          ],
        ],
        'subject' => [
          'value' => null,
          'name' => 'subject',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.subject'
          ]
        ],
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ],
      ]
    ],
    //Child Category - send-sms-to-external-data
    'send-sms-to-external-data' => [
      'systemName' => 'send-sms-to-external-data',
      'parentSystemName' => 'external-data-comunication', //from parent
      'en' => ['title' => 'Send SMS'],
      'es' => ['title' => 'Enviar sms'],
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],
    //Child Category - send-telegram-to-external-data
    'send-telegram-to-external-data' => [
      'systemName' => 'send-telegram-to-external-data',
      'parentSystemName' => 'external-data-comunication', //from parent
      'en' => ['title' => 'Send telegram'],
      'es' => ['title' => 'Enviar telegram'],
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'isTranslatable' => true,
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],
    //Child Category - send-whatsapp-to-external-data
    'send-whatsapp-to-external-data' => [
      'systemName' => 'send-whatsapp-to-external-data',
      'parentSystemName' => 'external-data-comunication', //from parent
      'en' => ['title' => 'Send whatsapp'],
      'es' => ['title' => 'Enviar whatsapp'],
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'isTranslatable' => true,
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],

     //PARENT - Child Category - client-communication
     'client-communication' => [
      'systemName' => 'client-communication',
      'en' => ['title' => 'Client Communication'],
      'es' => ['title' => 'Comunicacion con el Cliente'],
      'parentSystemName' => 'external-communication', //from parent
    ],
    //Child Category - send-email-to-requested-by
    'send-email-to-requested-by' => [
      'systemName' => 'send-email-to-requested-by',
      'parentSystemName' => 'client-communication', //from parent
      'en' => ['title' => 'Send Email'],
      'es' => ['title' => 'Enviar email'],
      'formFields' => [
        'from' => [
          'value' => null,
          'name' => 'from',
          'type' => 'select',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.quser.users',
            'filterByQuery' => true,
            'select' => ['label' => 'email', 'value' => 'email', 'id' => 'email']
          ],
          'props' => [
            'label' => 'requestable::common.formFields.from',
            'multiple' => false,
            'clearable' => true,
          ],
        ],
        'subject' => [
          'value' => null,
          'name' => 'subject',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.subject'
          ]
        ],
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ],
      ]
    ],
    //Child Category - send-sms-to-requested-by
    'send-sms-to-requested-by' => [
      'systemName' => 'send-sms-to-requested-by',
      'parentSystemName' => 'client-communication', //from parent
      'en' => ['title' => 'Send SMS'],
      'es' => ['title' => 'Enviar sms'],
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'isTranslatable' => true,
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],
    //Child Category - send-telegram-to-requested-by
    'send-telegram-to-requested-by' => [
      'systemName' => 'send-telegram-to-requested-by',
      'parentSystemName' => 'client-communication', //from parent
      'en' => ['title' => 'Send telegram'],
      'es' => ['title' => 'Enviar telegram'],
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'isTranslatable' => true,
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],
    //Child Category - send-whatsapp-to-requested-by
    'send-whatsapp-to-requested-by' => [
      'systemName' => 'send-whatsapp-to-requested-by',
      'parentSystemName' => 'client-communication', //from parent
      'en' => ['title' => 'Send whatsapp'],
      'es' => ['title' => 'Enviar whatsapp'],
      'formFields' => [
        'message' => [
          'value' => null,
          'name' => 'message',
          'type' => 'expression',
          'loadOptions' => [
            'apiRoute' => 'apiRoutes.qrequestable.categoriesFormFields',
            'select' => ['label' => 'label', 'id' => 'value'],
            'parametersUrl' => [
              'categoryId' => 1
            ]
          ],
          'isTranslatable' => true,
          'props' => [
            'label' => 'requestable::common.formFields.message',
            'type' => 'textarea',
            'rows' => 3,
          ]
        ]
      ]
    ],


  ],


  /*
  |--------------------------------------------------------------------------
  | Documentation
  |--------------------------------------------------------------------------
  */
  'documentation' => [
    'requestables' => "requestable::cms.documentation.requestables",
    'categories' => "requestable::cms.documentation.categories",
    'statuses' => "requestable::cms.documentation.statuses",
  ],

  /*
  |--------------------------------------------------------------------------
  | Configuration to comment types by entity
  |--------------------------------------------------------------------------
  */
  'commentableConfig' => [

    'requestable' => [

      'notification' => [
        'type' => 'notification',
        'icon'  => 'fa fa-bell',
        'color' => 'blue-5' //blue
      ],
      'document' => [
        'type' => 'document',
        'icon'  => 'fa fa-book',
        'color' => 'red-5' //red
      ],
      'statusChanged' => [
        'type' => 'statusChanged',
        'icon'  => 'fa fa-info-circle',
        'color' => 'yellow-9' //yellow
      ],
      'responsibleChanged' => [
        'type' => 'responsibleChanged',
        'icon'  => 'fa fa-info-circle',
        'color' => 'green-9' //green
      ]

    ]

  ]

];
