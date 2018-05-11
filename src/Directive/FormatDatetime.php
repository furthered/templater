<?php

namespace Templater\Directive;

class FormatDatetime extends Directive {

    protected function getName()
    {
        return 'datetime';
    }

    protected function directive($expression)
    {
        return "<?php echo Carbon\Carbon::parse($expression)->format('F j, Y g:ia T'); ?>";
    }

}
