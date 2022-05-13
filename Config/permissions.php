<?php

return [
    'requestable.requestables' => [
        'manage' => 'requestable::requestables.manage resource',
        'index' => 'requestable::requestables.list resource',
        'index-all' => 'requestable::requestables.index-all resource',
        'create' => 'requestable::requestables.create resource',
        'edit' => 'requestable::requestables.edit resource',
        'destroy' => 'requestable::requestables.destroy resource',
        'edit-status' => 'requestable::requestables.edit-status resource',
        'edit-eta-date' => 'requestable::requestables.edit-eta-date resource',
        'edit-created-by' => 'requestable::requestables.edit-created-by resource',
    ],

    'requestable.categories' => [
        'manage' => 'requestable::categories.manage resource',
        'index' => 'requestable::categories.list resource',
        'create' => 'requestable::categories.create resource',
        'edit' => 'requestable::categories.edit resource',
        'destroy' => 'requestable::categories.destroy resource',
        'restore' => 'requestable::categories.restore resource',
    ],
    'requestable.statuses' => [
        'manage' => 'requestable::statuses.manage resource',
        'index' => 'requestable::statuses.list resource',
        'create' => 'requestable::statuses.create resource',
        'edit' => 'requestable::statuses.edit resource',
        'destroy' => 'requestable::statuses.destroy resource',
        'restore' => 'requestable::statuses.restore resource',
    ],
    'requestable.fields' => [
        'manage' => 'requestable::fields.manage resource',
        'index' => 'requestable::fields.list resource',
        'create' => 'requestable::fields.create resource',
        'edit' => 'requestable::fields.edit resource',
        'destroy' => 'requestable::fields.destroy resource',
        'restore' => 'requestable::fields.restore resource',
    ],
    'requestable.statushistories' => [
        'manage' => 'requestable::statushistories.manage resource',
        'index' => 'requestable::statushistories.list resource',
        'create' => 'requestable::statushistories.create resource',
        'edit' => 'requestable::statushistories.edit resource',
        'destroy' => 'requestable::statushistories.destroy resource',
        'restore' => 'requestable::statushistories.restore resource',
    ],
// append







];
