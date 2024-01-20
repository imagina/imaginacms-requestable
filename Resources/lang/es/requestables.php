<?php

return [
    'list resource' => 'List requestables',
    'create resource' => 'Create requestables',
    'edit resource' => 'Edit requestables',
    'destroy resource' => 'Destroy requestables',
    'title' => [
        'requestables' => 'Requestable',
        'create requestable' => 'Create a requestable',
        'edit requestable' => 'Edit a requestable',
    ],
    'button' => [
        'create requestable' => 'Create a requestable',
    ],
    'table' => [
        'category' => 'Categoria',
        'status' => 'Estado',
        'status old' => 'Estado Anterior',
        'status new' => 'Estado Nuevo',
        'type' => 'Tipo',
        'requested by' => 'Solicitado por',
        'created by' => 'Peticion creada por',
        'last comment' => 'Ultimo comentario',
        'date' => 'Fecha y Hora',
        'hour' => 'Hora del cambio',
        'id' => 'Petición ID',
        'history created by' => 'Cambio realizado por'
    ],
    'form' => [
    ],
    'messages' => [
      "creatingSameRequestError" => "Ya hay una solicitud en proceso, para realizar otra solicitud debe esperar a que culmine la solicitud actual"
    ],
    'validations' => [
      'chatRequestableIdRequired' => "El id del lead no es válido",
      'chatRequestedByIdRequired' => "El lead no tiene un contacto asignado",
      'chatRequestedByPhoneNumberRequired' => "El contacto asignado no tiene un número de teléfono válido",
    ],
    'responsible' => [
        'updated' => "Se ha actualizado el responsable a: :responsible"
    ]
];
