<?php

namespace PhpOrient;
use PhpOrient\Configuration\Constants;

class TestCase extends \PHPUnit_Framework_TestCase {
    /**
     * @return array the test server config
     */
    protected static function getConfig() {
        $config                   = json_decode( file_get_contents( __DIR__ . '/../test-server.json' ), true );
        Constants::$LOGGING       = $config[ 'logging' ];
        Constants::$LOG_FILE_PATH = $config[ 'log_file_path' ];
        return $config;
    }

    /**
     * @return Client
     */
    protected static function createClient() {
        $config = static::getConfig();
        $client = new Client();
        $client->configure( array(
                'username' => $config[ 'username' ],
                'password' => $config[ 'password' ],
                'hostname' => $config[ 'hostname' ],
                'port'     => $config[ 'port' ],
        ) );

        return $client;
    }
}
