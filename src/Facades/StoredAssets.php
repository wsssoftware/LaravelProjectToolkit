<?php

namespace LaravelToolkit\Facades;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Facade;
use LaravelToolkit\StoredAssets\FilenameStoreType;
use LaravelToolkit\StoredAssets\StoredAssetModel;

/**
 * @method static string basePath()
 * @method static int clearTrashBin(string $disk)
 * @method static string defaultDisk()
 * @method static FilenameStoreType defaultFilenameStoreType()
 * @method static bool deleteFromTrashBin(string $disk, string $uuid)
 * @method static bool isValidUuidAsset(string $uuid)
 * @method static StoredAssetModel newModel(array $attributes = [])
 * @method static string modelFQN()
 * @method static Builder modelQuery()
 * @method static bool moveToTrashBin(string $disk, string $uuid)
 * @method static string path(string $uuid, ?string $path = null)
 * @method static bool restoreFromTrashBin(string $disk, string $uuid)
 * @method static int subdirectoryChars()
 * @method static int trashBinDeadlineTimestamp(Carbon $from = null)
 * @method static string trashBinPath(string $path = null)
 *
 * @see \LaravelToolkit\StoredAssets\StoredAssets
 */
class StoredAssets extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \LaravelToolkit\StoredAssets\StoredAssets::class;
    }
}
