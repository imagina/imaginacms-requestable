<?php

namespace Modules\Requestable\Repositories\Cache;

use Modules\Requestable\Repositories\SourceRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheSourceDecorator extends BaseCacheCrudDecorator implements SourceRepository
{
    public function __construct(SourceRepository $source)
    {
        parent::__construct();
        $this->entityName = 'requestable.sources';
        $this->repository = $source;
    }
}
