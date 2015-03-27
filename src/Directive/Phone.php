<?php

namespace Templater\Directive;

class Phone extends Directive {

    protected function directive($view, $compiler)
    {
        $pattern = $compiler->createMatcher('phone');

        return preg_replace($pattern, '<?php echo Format::phone($2); ?>', $view);
    }

}
