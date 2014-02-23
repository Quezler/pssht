<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Clicky\Pssht\Messages\CHANNEL;

use Clicky\Pssht\MessageInterface;
use Clicky\Pssht\Wire\Encoder;
use Clicky\Pssht\Wire\Decoder;

/**
 * SSH_MSG_CHANNEL_FAILURE message (RFC 4254).
 */
class FAILURE implements MessageInterface
{
    protected $channel;

    public function __construct($channel)
    {
        $this->channel = $channel;
    }

    public static function getMessageId()
    {
        return 100;
    }

    public function serialize(Encoder $encoder)
    {
        $encoder->encodeUint32($this->channel);
        return $this;
    }

    public static function unserialize(Decoder $decoder)
    {
        return new static($decoder->decodeUint32());
    }

    public function getChannel()
    {
        return $this->channel;
    }
}
