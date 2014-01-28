<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Clicky\Pssht\Messages\CHANNEL\REQUEST;

use Clicky\Pssht\Wire\Decoder;
use Clicky\Pssht\Messages\CHANNEL\REQUEST\Base;

class   Shell
extends Base
{
    static protected function _unserialize(Decoder $decoder)
    {
        return array();
    }
}

