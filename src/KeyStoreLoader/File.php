<?php

/*
* This file is part of pssht.
*
* (c) François Poirotte <clicky@erebot.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Clicky\Pssht\KeyStoreLoader;

class File
{
    protected $store;

    public function __construct(\Clicky\Pssht\KeyStore $store = null)
    {
        if ($store === NULL) {
            $store = new \Clicky\Pssht\KeyStore();
        }

        $this->store    = $store;
    }

    public function load($user, $file)
    {
        if (!is_string($user)) {
            throw new \InvalidArgumentException();
        }

        if (!is_string($file)) {
            throw new \InvalidArgumentException();
        }

        $algos = \Clicky\Pssht\Algorithms::factory();
        $types = array(
            'ssh-dss',
            'ssh-rsa',
#            'ecdsa-sha2-nistp256',
#            'ecdsa-sha2-nistp384',
#            'ecdsa-sha2-nistp521',
        );

        foreach (file($file) as $line) {
            $fields = explode(' ', preg_replace('/\\s+/', ' ', trim($line)));
            $max    = count($fields);
            for ($i = 0; $i < $max; $i++) {
                if (in_array($fields[$i], $types, true)) {
                    $cls = $algos->getClass('PublicKey', $fields[$i]);
                    $this->store->add($user, $cls::loadPublic($fields[$i+1]));
                    break;
                }
            }
        }
    }

    public function loadBulk(array $bulk)
    {
        foreach ($bulk as $user => $files) {
            if (!is_string($user)) {
                throw new \InvalidArgumentException();
            }

            if (!is_array($files) && !is_string($files)) {
                throw new \InvalidArgumentException();
            }

            $files = (array) $files;
            foreach ($files as $file) {
                $this->load($user, $file);
            }
        }
        return $this;
    }

    public function getStore()
    {
        return $this->store;
    }
}
