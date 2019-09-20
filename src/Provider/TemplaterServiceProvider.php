<?php

namespace Templater\Provider;

use Illuminate\Support\ServiceProvider;

class TemplaterServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('format', 'Templater\Format\Format');

        foreach ($this->app->config->get('template-directives') as $directive) {
            // To allow for local custom directives
            $class = (class_exists($directive)) ? $directive : 'Templater\Directive\\' . $directive;

            $this->app->make($class)->register();
        }
    }

}
