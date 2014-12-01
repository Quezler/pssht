<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace fpoirotte\Pssht\Handlers\KEXDH;

/**
 * Handler for SSH_MSG_KEXDH_INIT messages.
 */
class INIT implements \fpoirotte\Pssht\HandlerInterface
{
    // SSH_MSG_KEXDH_INIT = 30
    public function handle(
        $msgType,
        \fpoirotte\Pssht\Wire\Decoder $decoder,
        \fpoirotte\Pssht\Transport $transport,
        array &$context
    ) {
        $message    = \fpoirotte\Pssht\Messages\KEXDH\INIT::unserialize($decoder);
        $kexAlgo    = $context['kexAlgo'];
        $kexAlgo    = new $kexAlgo();
        $hostAlgo   = null;
        foreach ($context['kex']['client']->getServerHostKeyAlgos() as $algo) {
            if (isset($context['serverKeys'][$algo])) {
                $hostAlgo = $algo;
                break;
            }
        }
        if ($hostAlgo === null) {
            throw new \RuntimeException();
        }

        $logging    = \Plop\Plop::getInstance();
        $response   = new \fpoirotte\Pssht\Messages\KEXDH\REPLY(
            $message,
            $context['serverKeys'][$hostAlgo],
            $transport->getEncryptor(),
            $kexAlgo,
            $context['kex']['server'],
            $context['kex']['client'],
            $context['identity']['server'],
            $context['identity']['client']
        );

        $secret = gmp_strval($response->getSharedSecret(), 16);
        $logging->debug(
            "Shared secret:\r\n%s",
            array(
                wordwrap($secret, 16, ' ', true)
            )
        );

        $logging->debug(
            'Hash: %s',
            array(
                wordwrap(bin2hex($response->getExchangeHash()), 16, ' ', true)
            )
        );

        if (!isset($context['sessionIdentifier'])) {
            $context['sessionIdentifier'] = $response->getExchangeHash();
        }
        $context['DH'] = $response;
        $transport->writeMessage($response);
        return true;
    }
}
