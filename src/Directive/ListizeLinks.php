<?php

namespace Templater\Directive;

class ListizeLinks extends Directive {

    protected function getName()
    {
        return 'linkList',
    }

    protected function directive($expression)
    {
        return "<?php echo Format::toListLinks{$expression}; ?>";
    }

}
