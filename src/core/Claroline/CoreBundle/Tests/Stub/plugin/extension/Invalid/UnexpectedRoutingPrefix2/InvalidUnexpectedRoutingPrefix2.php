<?php

namespace Invalid\UnexpectedRoutingPrefix2;

use Claroline\CoreBundle\Library\Plugin\ClarolineExtension;

class InvalidUnexpectedRoutingPrefix2 extends ClarolineExtension
{
    public function getRoutingPrefix()
    {
        return '';
    }
}