<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Clicky\Pssht;

interface PublicKeyInterface
{
    public static function getName();

    public static function loadPrivate($pem, $passphrase = '');

    public static function loadPublic($b64);
}
