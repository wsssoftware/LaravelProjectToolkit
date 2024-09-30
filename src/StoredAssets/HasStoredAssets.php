<?php

namespace LaravelToolkit\StoredAssets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasStoredAssets
{
    private static array $storedAssetFields = [];

    private static array $storedAssetUuidMap = [];

    protected static function bootHasStoredAssets(): void
    {
        self::initStoredAssetFieldsAndRelations();
        self::saving(function (Model $model) {
            foreach ($model->getDirty() as $field => $item) {
                if (! $item instanceof Recipe) {
                    continue;
                } elseif (Str::isUuid($uuid = $item->save())) {
                    $model->setAttribute($field, $uuid);
                } else {
                    return false;
                }
            }

            return true;
        });
    }

    private static function initStoredAssetFieldsAndRelations(): void
    {
        $model = static::newModelInstance();
        foreach ($model->getCasts() as $field => $cast) {
            $validClass = class_exists($cast) && is_subclass_of($cast, Recipe::class);
            if ($validClass && ! $model->isRelation($field)) {
                $uuidField = "{$field}_uuid";
                static::$storedAssetFields[$field] = $field;
                static::$storedAssetUuidMap[$uuidField] = $field;
                static::resolveRelationUsing(
                    $field,
                    fn (Model $model) => $model->morphOne(StoredAssetModel::class, 'asset', 'model', 'id', $uuidField)
                );
            }
        }
    }

    public function setAttribute($key, $value)
    {
        if (isset(self::$storedAssetFields[$key])) {
            if ($this->hasCast($key)) {
                /** @var class-string<\LaravelToolkit\StoredAssets\Recipe> $cast */
                $cast = $this->casts()[$key];
                $value = $cast::parse($this, $key, $value);
            }
        }

        return parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        if (isset(self::$storedAssetUuidMap[$key])) {
            return Arr::get($this->getAttributes(), self::$storedAssetUuidMap[$key]);
        }

        return parent::getAttribute($key);
    }
}
