<?php

use LaravelToolkit\Actions\Sitemap\RenderSitemap;
use LaravelToolkit\Facades\Sitemap;

it('can render', function () {
    config()->set('laraveltoolkit.sitemap.timeout', 120);
    $this->get(route('lt.sitemap'))
        ->assertSuccessful();
    $lastModified = filemtime(base_path('routes/sitemap.php'));
    $cacheKey = 'lt.sitemap.'.sha1('localhost'.'::::'.$lastModified);
    expect(Cache::has($cacheKey))->toBeTrue();
});

it('can render without cache', function () {
    config()->set('laraveltoolkit.sitemap.cache', false);
    $this->get(route('lt.sitemap'))
        ->assertSuccessful();
    $lastModified = filemtime(base_path('routes/sitemap.php'));
    $cacheKey = 'lt.sitemap.'.sha1('localhost'.'::::'.$lastModified);
    expect(Cache::has($cacheKey))->toBeFalse();
});

it('log on large files', function () {
    config()->set('laraveltoolkit.sitemap.max_file_size', 1024);
    $rs = new RenderSitemap;
    Sitemap::addUrl(str_repeat('1', 2_000));
    Log::shouldReceive('warning')
        ->with('The sitemap file size limit of 1.00 KB was exceeded in 1.08 KB. This may cause search engines to reject it.')
        ->andThrow(Exception::class, 'large files');

    expect(fn () => $rs(request()))
        ->toThrow('large files');
});

it('log on large items count', function () {
    config()->set('laraveltoolkit.sitemap.max_file_items', 100);
    /** @var \Illuminate\Support\Collection $items */
    $items = (new ReflectionClass(Sitemap::getFacadeRoot()::class))
        ->getProperty('items')
        ->getValue(Sitemap::getFacadeRoot());
    $rs = new RenderSitemap;
    for ($i = 1; $i <= 101; $i++) {
        $items->push(1);
    }
    Log::shouldReceive('warning')
        ->with('The sitemap items count limit of 100 was exceeded in 1. This may cause search engines to reject it.')
        ->andThrow(Exception::class, 'large count');

    expect(fn () => $rs(request()))
        ->toThrow('large count');
});
