<?php

namespace App\Observers;

use App\Support\FrontendCache;

class FrontendCacheObserver
{
    public function created(object $model): void
    {
        FrontendCache::flush();
    }

    public function updated(object $model): void
    {
        FrontendCache::flush();
    }

    public function deleted(object $model): void
    {
        FrontendCache::flush();
    }

    public function restored(object $model): void
    {
        FrontendCache::flush();
    }
}
