<?php

namespace Templater\Directive;

class Listize extends Directive {

    protected function getName()
    {
        return 'list',
    }

    protected function directive($expression)
    {
        return "<?php echo Format::toList{$expression}; ?>";
    }

}
