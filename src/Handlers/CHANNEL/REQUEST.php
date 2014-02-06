<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Clicky\Pssht\Handlers\CHANNEL;

class REQUEST extends Base
{
    // SSH_MSG_CHANNEL_REQUEST = 98
    public function handle(
        $msgType,
        \Clicky\Pssht\Wire\Decoder $decoder,
        \Clicky\Pssht\Transport $transport,
        array &$context
    ) {
        $encoder    = new \Clicky\Pssht\Wire\Encoder();
        $channel    = $decoder->decodeUint32();
        $type       = $decoder->decodeString();
        $wantsReply = $decoder->decodeBoolean();

        $encoder->encodeUint32($channel);
        $encoder->encodeString($type);
        $encoder->encodeBoolean($wantsReply);
        $decoder->getBuffer()->unget($encoder->getBuffer()->get(0));
        $remoteChannel = $this->connection->getChannel($channel);

        switch ($type) {
            case 'exec':
            case 'shell':
            case 'pty-req':
                // Normalize the name.
                // Eg. "pty-req" becomes "PtyReq".
                $cls = str_replace(' ', '', ucwords(str_replace('-', ' ', $type)));
                $cls = '\\Clicky\\Pssht\\Messages\\CHANNEL\\REQUEST\\' . $cls;
                $message = $cls::unserialize($decoder);
                break;

            default:
                if ($wantsReply) {
                    $response = new \Clicky\Pssht\Messages\CHANNEL\FAILURE($remoteChannel);
                    $transport->writeMessage($response);
                }
                return true;
        }

        if (!$wantsReply) {
            return true;
        }

        if (in_array($type, array('shell', 'exec'), true)) {
            $response = new \Clicky\Pssht\Messages\CHANNEL\SUCCESS($remoteChannel);
        } else {
            $response = new \Clicky\Pssht\Messages\CHANNEL\FAILURE($remoteChannel);
        }
        $transport->writeMessage($response);

        if (in_array($type, array('shell', 'exec'), true)) {
            $cls = $transport->getApplicationFactory();
            if ($cls !== null) {
                new $cls($transport, $this->connection, $message);
            }
        }

        return true;
    }
}
