<?php

namespace Templater\Directive;

class Listize extends Directive {

    protected function directive($view, $compiler)
    {
        $pattern = $compiler->createMatcher('list');

        return preg_replace($pattern, '<?php echo Format::toList$2; ?>', $view);
    }

}
