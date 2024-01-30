<?php

return [
    'requestable.requestables' => [
        'manage' => 'requestable::requestables.manage resource',
        'index' => 'requestable::requestables.list resource',
        'index-all' => 'requestable::requestables.index-all resource',
        'create' => 'requestable::requestables.create resource',
        'edit' => 'requestable::requestables.edit resource',
        'destroy' => 'requestable::requestables.destroy resource',
        'see-source' => 'requestable::requestables.see-source',
        'see-responsible' => 'requestable::requestables.see-responsible',
        'see-requested-by' => 'requestable::requestables.see-requested-by',
        'see-created-by' => 'requestable::requestables.see-created-by',
        'see-files' => 'requestable::requestables.see-files',
        'edit-files' => 'requestable::requestables.edit-files',
        'edit-status' => 'requestable::requestables.edit-status',
        'edit-eta-date' => 'requestable::requestables.edit-eta-date',
        'edit-source' => 'requestable::requestables.edit-source',
        'edit-responsible' => 'requestable::requestables.edit-responsible',
        'edit-requested-by' => 'requestable::requestables.edit-requested-by',
        'edit-created-by' => 'requestable::requestables.edit-created-by',
        'filter-created-by' => 'requestable::requestables.filter-created-by',
        'filter-source' => 'requestable::requestables.filter-source',
        'filter-requested-by' => 'requestable::requestables.filter-requested-by',
        'filter-status' => 'requestable::requestables.filter-status',
        'filter-responsible' => 'requestable::requestables.filter-responsible',
        'assign-responsible' => 'requestable::requestables.assign-responsible',
    ],
    'requestable.categories' => [
        'manage' => 'requestable::categories.manage resource',
        'index' => 'requestable::categories.list resource',
        'create' => 'requestable::categories.create resource',
        'edit' => 'requestable::categories.edit resource',
        'edit-events' => 'requestable::categories.edit-events resource',
        'edit-internal' => 'requestable::categories.edit-internal resource',
        'edit-requestable-type' => 'requestable::categories.edit-requestable-type resource',
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
    'requestable.categoryrules' => [
        'manage' => 'requestable::categoryrules.manage resource',
        'index' => 'requestable::categoryrules.list resource',
        'create' => 'requestable::categoryrules.create resource',
        'edit' => 'requestable::categoryrules.edit resource',
        'destroy' => 'requestable::categoryrules.destroy resource',
        'restore' => 'requestable::categoryrules.restore resource',
    ],
    'requestable.automationrules' => [
        'manage' => 'requestable::automationrules.manage resource',
        'index' => 'requestable::automationrules.list resource',
        'create' => 'requestable::automationrules.create resource',
        'edit' => 'requestable::automationrules.edit resource',
        'destroy' => 'requestable::automationrules.destroy resource',
        'restore' => 'requestable::automationrules.restore resource',
    ],
    'requestable.comments' => [
        'manage' => 'requestable::comments.manage resource',
        'index' => 'requestable::comments.list resource',
        'create' => 'requestable::comments.create resource',
        'edit' => 'requestable::comments.edit resource',
        'destroy' => 'requestable::comments.destroy resource',
        'restore' => 'requestable::comments.restore resource',
    ],
    'requestable.sources' => [
        'index-all' => 'requestable::sources.index-all resource',
        'manage' => 'requestable::sources.manage resource',
        'index' => 'requestable::sources.list resource',
        'create' => 'requestable::sources.create resource',
        'edit' => 'requestable::sources.edit resource',
        'destroy' => 'requestable::sources.destroy resource',
        'restore' => 'requestable::sources.restore resource',
    ],
// append










];
