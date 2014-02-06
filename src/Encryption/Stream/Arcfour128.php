<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Clicky\Pssht\Encryption\Stream;

class Arcfour128 extends Arcfour256
{
    public static function getName()
    {
        return 'arcfour128';
    }

    public static function getKeySize()
    {
        return 128 >> 3;
    }
}
