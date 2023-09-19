<?php

namespace Modules\Requestable\Events\Handlers;

use Modules\Media\Entities\File;
use Modules\Requestable\Events\RequestableIsUpdating;
use Modules\Iblog\Events\PostWasUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyNewFilesRequestable implements ShouldQueue
{

  use InteractsWithQueue;

  public function __construct()
  {
  }

  /**
   * Init handle
   */
  public function handle(RequestableIsUpdating $event)
  {
    $data = json_decode(json_encode($event->data));
    $requestableInDB = $event->requestable;
    $notificationService = app("Modules\Notification\Services\Inotification");
    $commentService = app("Modules\Icomments\Services\CommentService");

    $filesRequestable = \DB::table('media__imageables')->whereIn('imageable_id', $data->medias_multi->documents->files ?? [])
      ->where('imageable_type', '=', 'Modules\Requestable\Entities\Requestable')->get();

    $requestableFilesIds = [];

    foreach ($filesRequestable as $file) {
      array_push($requestableFilesIds, $file->file_id);
    }

    $newFilesInRequestable = array_diff($data->medias_multi->documents->files ?? [], $requestableFilesIds);

    $filesToNotify = \DB::table('media__files')->whereIn('id', $newFilesInRequestable)->get()->pluck('id');
    $usersToNotify = \DB::table('users')->whereIn('id', [$requestableInDB->requested_by_id, $requestableInDB->responsible_id])->get()->pluck('email');
    $files = File::whereIn("id", $filesToNotify->toArray())->get();
    $to["email"] = $usersToNotify;
    $to["broadcast"] = [$requestableInDB->requested_by_id, $requestableInDB->responsible_id];

    if (!empty($files)) {
      foreach ($files as $fileToNotify) {
        $notificationService->to($to)->push([
          "title" => trans('requestable::common.notifications.titleReportNewDocument'),
          "message" => trans('requestable::common.notifications.MessageReportNewDocument') . $fileToNotify->url,
          "link" => $fileToNotify->url,
          "isAction" => true,
          "setting" => ["saveInDatabase" => 1]
        ]);
        $commentService->create($requestableInDB->model, [
            "comment" => trans('requestable::common.notifications.MessageReportNewDocument') . $fileToNotify->url,
            "internal" => true,
            "type" => "notification"
          ]
        );
      }
    }
  }
}