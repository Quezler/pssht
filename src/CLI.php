<?php
/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Clicky\Pssht\Client;

function escape($data)
{
    return addcslashes($data, "\x00..\x1F\x7F..\xFF");
}

function main()
{
    $container  = new ContainerBuilder();
    $loader     = new XmlFileLoader($container, new FileLocator(getcwd()));
    $loader->load('pssht.xml');

    $logging    = Plop::getInstance();
    $sockets    = array('servers' => array(), 'clients' => array());
    $clients    = array();

    $listen     = (array) $container->getParameter('listen');
    foreach ($listen as $spec) {
        $socket                 = stream_socket_server("tcp://$spec");
        $sockets['servers'][]   = $socket;
        $address                = stream_socket_get_name($socket, false);
        $logging->info("Listening for new connections on %s", array($address));
    }

    while (true) {
        $read   = array_merge($sockets['servers'], $sockets['clients']);
        $except = $read;
        $write  = array();

        foreach ($clients as $id => $client) {
            if (count($client->getEncoder()->getBuffer())) {
                $write[] = $sockets['clients'][$id];
            }
        }

        if (@stream_select($read, $write, $except, null) === false) {
            $logging->error(
                'Error while waiting for activity on sockets: %s',
                array(socket_strerror(socket_last_error()))
            );
            continue;
        }

        foreach ($read as $socket) {
            if (in_array($socket, $sockets['servers'], true)) {
                $new = stream_socket_accept($socket);
                if ($new === false) {
                    $logging->error(
                        'Could not accept new client: %s',
                        array(socket_strerror(socket_last_error()))
                    );
                    continue;
                }

                for ($id = 0; isset($sockets['clients'][$id]); $id++) {
                    // Nothing to do.
                }
                $sockets['clients'][$id] = $new;
                $peer   = stream_socket_get_name($new, true);
                $logging->info(
                    '#%(id)d New client connected from %(peer)s',
                    array('id' => $id, 'peer' => $peer)
                );
                $clients[$id] = $container->get('client');
                continue;
            }

            $data = fread($socket, 8192);
            if ($data === '') {
                $peer   = stream_socket_get_name($socket, true);
                $id     = array_search($socket, $sockets['clients'], true);
                $logging->info(
                    '#%(id)d Client disconnected from %(peer)s',
                    array('id' => $id, 'peer' => $peer)
                );
                fclose($socket);
                unset($sockets['clients'][$id]);
                unset($clients[$id]);
                continue;
            }

            if ($data !== false) {
                $peer   = stream_socket_get_name($socket, true);
                $length = strlen($data);
                $id     = array_search($socket, $sockets['clients'], true);
                $clients[$id]->getDecoder()->getBuffer()->push($data);

                $logging->debug(
                    '#%(id)d Received %(length)d bytes from %(peer)s',
                    array('id' => $id, 'peer' => $peer, 'length' => $length)
                );
                $logging->debug('%s', array(escape($data)));

                // Process messages in the buffer.
                while ($clients[$id]->readMessage()) {
                    // Each message gets processed by readMessage().
                }
            }
        }

        foreach ($write as $socket) {
            $id = array_search($socket, $sockets['clients'], true);
            if ($id === false) {
                continue;
            }

            $peer   = stream_socket_get_name($socket, true);
            $buffer = $clients[$id]->getEncoder()->getBuffer();
            $size   = count($buffer);
            $data   = $buffer->get($size);
            while ($size > 0) {
                $written = fwrite($socket, $data);
                if ($written === false) {
                    break;
                }

                $logging->debug(
                    "#%(id)d Sent %(written)d bytes to %(peer)s",
                    array('id' => $id, 'peer' => $peer, 'written' => $written)
                );
                $logging->debug('%s', array(escape(substr($data, 0, $written))));
                $data   = substr($data, $written);
                $size  -= $written;
            }
        }
    }
}
