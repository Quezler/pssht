<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Clicky\Pssht\MAC\EtM\SHA1;

use \Clicky\Pssht\MAC\EtM\EtMInterface;

class Len96 extends \Clicky\Pssht\MAC\SHA1\Len96 implements EtMInterface
{
    public static function getName()
    {
        return 'hmac-sha1-96-etm@openssh.com';
    }
}
