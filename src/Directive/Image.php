<?php

namespace Templater\Directive;

class Image extends Directive {

    protected function directive($view, $compiler)
    {
        $pattern = $compiler->createMatcher('image');

        return preg_replace($pattern, '<?php echo Assets::image()->dynamic$2; ?>', $view);
    }

}
