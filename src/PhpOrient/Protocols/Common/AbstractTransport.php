<?php

namespace PhpOrient\Protocols\Common;

use PhpOrient\Configuration\Constants as ClientConstants;
use PhpOrient\Exceptions\PhpOrientException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
     * @var ClustersMap This Handle the actual Cluster List of Current Database
     */
    protected $clusterMap;

    /**
     * @var OrientNode[] This is the list of OrientDB Nodes when distributed
     */
    protected $nodesList;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var OrientVersion
     */
    protected $orientVersion;

    /**
     * Class Constructor
     *
     * @throws PhpOrientException
     */
    public function __construct() {
        $this->setLogger();
    }

    /**
     * Set the client Logger
     *
     * @throws PhpOrientException
     */
    public function setLogger(){

        if ( ClientConstants::$LOGGING ) {

            if ( $this->_logger === null ) {

                if( !class_exists( '\Monolog\Logger', true ) ){
                    throw new PhpOrientException( "No development environment installed from composer. Try 'composer update' or remove logging from client constants ( \\PhpOrient\\Configuration\\Constants::\$LOGGING )" );
                }

                $this->_logger = new \Monolog\Logger( get_class( $this ) );
                $file_path     = "php://stdout";

                if ( ClientConstants::$LOG_FILE_PATH ) {
                    $file_path = ClientConstants::$LOG_FILE_PATH;
                }

                $handler = new \Monolog\Handler\StreamHandler( $file_path, \Monolog\Logger::DEBUG );
                $handler->setFormatter( new \Monolog\Formatter\LineFormatter( null, null, false, true ) );
                $this->_logger->pushHandler( $handler );

            }

        } else {
            $this->_logger = new NullLogger();
        }

    }

    /**
     * Get the Logger from transport
     *
     * @return LoggerInterface
     * @throws PhpOrientException
     */
    public function getLogger(){
        if( empty( $this->_logger ) ) {
            $this->setLogger();
        }
        return $this->_logger;
    }

    /**
     * Debug method
     *
     * @param $message
     */
    public function debug( $message ){
        $this->_logger->debug( $message );
    }

    /**
     * @return ClustersMap
     */
    public function getClusterMap() {
        return $this->clusterMap;
    }

    /**
     * @param ClustersMap $clusterList
     */
    public function setClustersMap( ClustersMap $clusterList ) {
        $this->clusterMap = $clusterList;
    }

    /**
     * Retrieve a new transaction instance
     *
     * @return \PhpOrient\Protocols\Binary\Transaction\TxCommit
     */
    public function getTransaction(){
        return new \PhpOrient\Protocols\Binary\Transaction\TxCommit( $this );
    }

    /**
     * Retrieve the nodes list, optionally filter excluding the actual one
     *
     * //TODO Improve with different protocol types handler if another transport protocol are implemented
     *
     * @param bool|false $filterActualNode
     *
     * @return OrientNode[]
     */
    public function getNodesList( $filterActualNode = false ) {
        $list = [];
        if( $filterActualNode && !empty( $this->nodesList ) ){
            $list = $this->nodesList;
            foreach( $list as $pos => $node ){
                if( $node->host == $this->hostname && $node->port == $this->port ){
                    unset( $list[$pos] );
                }
            }
        }
        return $list;
    }

    /**
     * @param OrientNode[] $nodesList
     */
    public function setNodesList( $nodesList ) {
        $this->nodesList = $nodesList;
    }

    /**
     * @return OrientVersion
     */
    public function getOrientVersion() {
        return $this->orientVersion;
    }

    /**
     * @param OrientVersion $orientVersion
     */
    public function setOrientVersion( $orientVersion ) {
        $this->orientVersion = $orientVersion;
    }

}
