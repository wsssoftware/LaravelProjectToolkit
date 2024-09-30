<?php

namespace LaravelToolkit\StoredAssets\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use LaravelToolkit\Facades\StoredAssets;

class GarbageCollectorManager implements ShouldBeUnique, ShouldQueue
{
    use HasDisk, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected ?array $disks = null,
    ) {
        $this->disks = $this->disks ?? [StoredAssets::defaultDisk()];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        GarbageCollector::clearCount();
        $chain = [];
        foreach ($this->disks as $diskName) {
            $chain[] = new TrashBinCleaner($diskName);
            $disk = $this->disk($diskName);
            foreach (collect($disk->directories(StoredAssets::basePath()))->shuffle() as $directory) {
                if (str_contains($directory, config('laraveltoolkit.stored_assets.trash_bin.folder'))) {
                    continue;
                }
                $chain[] = new GarbageCollector($diskName, $directory);
            }
        }
        $chain[] = new GarbageCollectorSummary;
        Bus::chain($chain)
            ->onQueue($this->queue)
            ->onConnection($this->connection)
            ->dispatch();
    }
}
