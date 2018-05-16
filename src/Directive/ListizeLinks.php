<?php

namespace Templater\Directive;

class ListizeLinks extends Directive {

    protected function getName()
    {
        return 'linkList';
    }

    protected function directive($expression)
    {
        return "<?php echo (new Templater\Format\Format)->toListLinks({$expression}); ?>";
    }

}
