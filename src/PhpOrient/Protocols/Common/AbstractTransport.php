<?php

namespace PhpOrient\Protocols\Common;

use PhpOrient\Configuration\Constants as ClientConstants;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;

abstract class AbstractTransport implements TransportInterface {
    use ConfigurableTrait;

    /**
     * @var string The server host.
     */
    protected $hostname;

    /**
     * @var string The port for the server.
     */
    protected $port;

    /**
     * @var string The username for the server.
     */
    protected $username;

    /**
     * @var string The password for the server.
     */
    protected $password;

    /**
     * @var ClusterMap This Handle the actual Cluster List of Current Database
     */
    protected $clusterList;

    /**
     * @var LoggerInterface
     */
    protected static $_logger;

    /**
     * Class Constructor
     */
    public function __construct() {

        if ( ClientConstants::$LOGGING ) {

            if ( self::$_logger === null ) {
                self::$_logger = new Logger( get_class( $this ) );
                $file_path = "php://stdout";
                if ( ClientConstants::$LOG_FILE_PATH ) {
                    $file_path = ClientConstants::$LOG_FILE_PATH;
                }
                $handler = new StreamHandler( $file_path, Logger::DEBUG );
                $handler->setFormatter( new LineFormatter( null, null, false, true ) );
                self::$_logger->pushHandler( $handler );
            }

        } else {
            self::$_logger = new NullLogger();
        }

    }

    public function debug( $message ){
        self::$_logger->debug( $message );
    }

    /**
     * @return ClusterMap
     */
    public function getClusterMap() {
        return $this->clusterList;
    }

    /**
     * @param ClusterMap $clusterList
     */
    public function setClusterMap( ClusterMap $clusterList ) {
        $this->clusterList = $clusterList;
    }

}
