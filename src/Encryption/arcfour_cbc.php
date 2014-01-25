<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Clicky\Pssht\Encryption;

class   arcfour_cbc
extends \Clicky\Pssht\Encryption\Base
{
    const ALGORITHM = 'MCRYPT_ARCFOUR';

    static public function getName()
    {
        return 'arcfour-cbc';
    }
}

