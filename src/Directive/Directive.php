<?php

namespace Templater\Directive;

use Illuminate\View\Compilers\BladeCompiler;

abstract class Directive {

    public function register()
    {
        \Blade::directive($this->getName(), function($expression) {
            return $this->directive($expression);
        });
    }

    protected abstract function directive($expression);

    protected abstract function getName();

}
