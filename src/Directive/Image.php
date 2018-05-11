<?php

namespace Templater\Directive;

class Image extends Directive
{
    protected function getName()
    {
        return 'image';
    }

    protected function directive($expression)
    {
        return "<?php echo Assets::image()->fetch({$expression}); ?>";
    }
}
