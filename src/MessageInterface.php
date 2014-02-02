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

interface MessageInterface
{
    public static function getMessageId();

    public function serialize(\Clicky\Pssht\Wire\Encoder $encoder);

    public static function unserialize(\Clicky\Pssht\Wire\Decoder $encoder);
}
