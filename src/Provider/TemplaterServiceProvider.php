<?php

namespace Templater\Provider;

use Illuminate\Support\ServiceProvider;

class TemplaterServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->app->config->get('template-directives') as $directive) {
            $this->app->make('Templater\Directive\\' . $directive)->register();
        }
    }

}
