<?php

namespace LaravelToolkit\StoredAssets\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GarbageCollectorSummary implements ShouldQueue, ShouldBeUnique
{
    use Queueable, HasDisk;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $count = GarbageCollector::getCount();
        if ($count > 0) {
            Log::info(sprintf('On stored assets, %s item(s) was moved to trash bin.', $count));
        }
    }
}
