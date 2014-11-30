<?php

namespace PhpOrient;

class TestCase extends \PHPUnit_Framework_TestCase {
    /**
     * @return array the test server config
     */
    protected static function getConfig() {
        return json_decode( file_get_contents( __DIR__ . '/../test-server.json' ), true );
    }

    protected static function createClient() {
        $config = static::getConfig();
        $client = new Client();
        $client->configure( array(
                'username' => $config[ 'username' ],
                'password' => $config[ 'password' ],
                'hostname' => $config[ 'host' ],
                'port'     => $config[ 'port' ],
        ) );

        return $client;
    }
}
