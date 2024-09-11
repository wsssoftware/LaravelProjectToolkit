<?php

namespace LaravelToolkit\SEO;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SEOComponent extends Component
{

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('laraveltoolkit::seo', ['payload' => \LaravelToolkit\Facades\SEO::payload()]);
    }
}
