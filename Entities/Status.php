<?php

namespace Modules\Requestable\Entities;

/**
 * Class Status
 * @package Modules\Requestable\Entities
 */
class Status
{
    const PENDING = 0;
    const INPROGRESS = 1;
    const COMPLETED = 2;
    const CANCELLED = 3;

    /**
     * @var array
     */
    private $statuses = [];

    public function __construct()
    {
        $this->statuses = [
            self::PENDING => trans('requestable::common.status.pending'),
            self::INPROGRESS => trans('requestable::common.status.inProgress'),
            self::COMPLETED => trans('requestable::common.status.completed'),
            self::CANCELLED => trans('requestable::common.status.cancelled'),
        ];
    }

    /**
     * Get the available statuses
     * @return array
     */
    public function lists()
    {
        return $this->statuses;
    }

    /**
     * Get the request status
     * @param int $statusId
     * @return string
     */
    public function get($statusId)
    {
        if (isset($this->statuses[$statusId])) {
            return $this->statuses[$statusId];
        }

        return $this->statuses[self::PENDING];
    }
}
