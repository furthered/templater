<?php

namespace Templater\Directive;

class ListizeLinks extends Directive {

    protected function directive($view, $compiler)
    {
        $pattern = $compiler->createMatcher('linkList');

        return preg_replace($pattern, '<?php echo Format::toListLinks$2; ?>', $view);
    }

}
