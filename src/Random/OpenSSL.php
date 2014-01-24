<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Clicky\Pssht\Random;

class       OpenSSL
implements  \Clicky\Pssht\RandomInterface
{
    public function __construct()
    {
    }

    public function getBytes($count)
    {
        if (!is_int($count) || $count <= 0)
            throw new \InvalidArgumentException();
        $value = openssl_random_pseudo_bytes($count, $strong);
        /// @FIXME: warn user or tweak the value for crypto-weak values
        if (!$strong)
            ;
        return $value;
    }
}

