<?php

namespace Templater\Directive;

class Phone extends Directive {

    protected function getName()
    {
        return 'phone';
    }

    protected function directive($expression)
    {
        return "<?php echo (new Templater\Format\Format)->phone($expression); ?>";
    }

}
