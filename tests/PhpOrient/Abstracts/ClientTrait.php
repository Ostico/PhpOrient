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

    public static  $DATABASE = null;
    public static  $HOST_NAME = null;

    public static function setEnv(){
        static::$DATABASE = getenv( 'DBNAME' );
        if ( empty( static::$DATABASE ) ){
            static::$DATABASE = 'demodb';
        }

        static::$HOST_NAME = getenv( 'HostName' );
        if ( empty( static::$HOST_NAME ) ){
            static::$HOST_NAME = '192.168.1.1';
        }
    }

    /**
     * @param string $type
     *
     * @return mixed the test server config
     */
    protected static function getConfig( $type = '' ) {

        static::setEnv();

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
        $config[ 'hostname' ] = static::$HOST_NAME;

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