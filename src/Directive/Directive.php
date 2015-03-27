<?php

namespace Templater\Directive;

use Illuminate\View\Compilers\BladeCompiler;

abstract class Directive {

    public function register()
    {
        \Blade::extend(function ($view, $compiler) {
            return $this->directive($view, $compiler);
        });
    }

    protected abstract function directive($view, $compiler);

}
