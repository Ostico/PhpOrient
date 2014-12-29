<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 15/12/14
 * Time: 1.48
 * 
 */

namespace PhpOrient\Abstracts;

use PhpOrient\PhpOrient;
use PhpOrient\Configuration\Constants as ClientConstants;

trait ClientTrait {

    /**
     * @param string $type
     *
     * @return mixed the test server config
     */
    protected static function getConfig( $type = '' ) {

        $config = json_decode( file_get_contents( __DIR__ . '/../../test-server.json' ), true );
        switch ( $type ){
            case 'connect':
            case 'open':
                $config[ 'username' ] = $config[ $type ]['username'];
                $config[ 'password' ] = $config[ $type ]['password'];
                break;
            default:
                $config[ 'username' ] = $config[ 'open' ]['username'];
                $config[ 'password' ] = $config[ 'open' ]['password'];
        }

        ClientConstants::$LOGGING       = $config[ 'logging' ];
        ClientConstants::$LOG_FILE_PATH = $config[ 'log_file_path' ];
        return $config;
    }

    /**
     * @param string $type
     *
     * @return PhpOrient
     * @throws \Exception
     */
    protected function createClient( $type = '' ) {
        $config = static::getConfig( $type );
        $client = new PhpOrient();
        $client->configure( array(
            'username' => $config[ 'username' ],
            'password' => $config[ 'password' ],
            'hostname' => $config[ 'hostname' ],
            'port'     => $config[ 'port' ],
        ) );

        return $client;
    }

}