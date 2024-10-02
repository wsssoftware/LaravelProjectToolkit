<?php

namespace LaravelToolkit\SEO;


use Illuminate\Support\Collection;

readonly class RobotsTxtRule
{
    public null|Collection $allow;
    public null|Collection $disallow;

    public function __construct(
        public string $userAgent,
        Collection|array $allow,
        Collection|array $disallow
    ) {
        $this->allow = is_array($allow) ? collect($allow) : $allow;
        $this->disallow = is_array($disallow) ? collect($disallow) : $disallow;
    }
}
