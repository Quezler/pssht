<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Clicky\Pssht\Messages;

use Clicky\Pssht\MessageInterface;
use Clicky\Pssht\Wire\Encoder;
use Clicky\Pssht\Wire\Decoder;

class       SERVICE_ACCEPT
implements  MessageInterface
{
    const MESSAGE_ID = 6;

    protected $_service;

    public function __construct($service)
    {
        if (!is_string($service))
            throw new \InvalidArgumentException();
        $this->_service = $service;
    }

    public function serialize(Encoder $encoder)
    {
        $encoder->encode_string($this->_service);
    }

    static public function unserialize(Decoder $decoder)
    {
        return new self($decoder->decode_string());
    }

    public function getServiceName()
    {
        return $this->_service;
    }
}

