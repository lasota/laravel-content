<?php

namespace Lasota\LaravelContent;

use Illuminate\Support\ServiceProvider;

class LaravelContentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton('content', fn () => new Repository());

        $contentLoader = new LoadContent();
        $contentLoader->loadContent($this->app, $this->app->get('content'));
    }
}