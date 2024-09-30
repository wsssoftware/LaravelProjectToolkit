<?php

use LaravelToolkit\StoredAssets\Assets;
use LaravelToolkit\StoredAssets\StoredAssetModel;
use LaravelToolkit\Tests\Model\Product;

it('test model features', function () {
    $uuid = Str::uuid()->toString();
    $storedAsset = StoredAssetModel::create([
        'id' => $uuid,
        'model' => Product::class,
        'field' => 'image',
        'assets' => new Assets()
    ]);
    $product = Product::create([
        'id' => 1,
        'image' => $uuid,
    ]);

    expect($storedAsset->assetable->id)
        ->toEqual($product->id);

    $product->delete();

    expect($storedAsset->refresh()->assetable?->id)
        ->toBeNull();
});
