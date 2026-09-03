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
            $templaterDirective = 'Templater\Directive\\' . $directive;

            if (class_exists($templaterDirective)) {
                $class = $templaterDirective;
            } elseif (class_exists($directive)) {
                // Allow fully qualified local custom directives.
                $class = $directive;
            } else {
                $class = $templaterDirective;
            }

            $this->app->make($class)->register();
        }
    }

}
