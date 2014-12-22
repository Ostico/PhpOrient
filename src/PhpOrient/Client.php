<?php

namespace PhpOrient;

use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Binary\Operations\RecordCreate;
use PhpOrient\Protocols\Binary\Operations\RecordDelete;
use PhpOrient\Protocols\Binary\Operations\RecordLoad;
use PhpOrient\Protocols\Binary\Operations\RecordUpdate;
use PhpOrient\Protocols\Common\ClusterMap;
use PhpOrient\Protocols\Common\ConfigurableInterface;
use PhpOrient\Protocols\Common\ConfigurableTrait;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Common\TransportInterface;
use PhpOrient\Protocols\Binary\SocketTransport;
use PhpOrient\Exceptions\TransportException;

/**
 * Class Client
 *
 * @package PhpOrient
 */
class Client implements ConfigurableInterface {
    use ConfigurableTrait;

    /**
     * @var string The server host.
     */
    public $hostname;

    /**
     * @var string The port for the server.
     */
    public $port;

    /**
     * @var string The username for the server.
     */
    public $username;

    /**
     * @var string The password for the server.
     */
    public $password;

    /**
     * @var TransportInterface The transport to use for the connection to the server.
     */
    protected $_transport;

    /**
     * Sets the transport
     *
     * @param \PhpOrient\Protocols\Common\TransportInterface $transport
     *
     * @return $this the current object
     */
    public function setTransport( TransportInterface $transport ) {
        $this->_transport = $this->createTransport( $transport );
        return $this;
    }

    /**
     * Gets the transport
     *
     * @return \PhpOrient\Protocols\Common\AbstractTransport
     */
    public function getTransport() {
        if ( $this->_transport === null ) {
            $this->_transport = $this->createTransport();
        }
        return $this->_transport;
    }

    /**
     * Start a new Transaction
     *
     * @return \PhpOrient\Protocols\Binary\Transaction\TxCommit
     */
    public function getTransactionStatement(){
       return $this->getTransport()->getTransaction();
    }

    /**
     * Create a transport instance.
     *
     * @param TransportInterface|null $transport
     *
     * @return Protocols\Binary\SocketTransport the transport interface
     * @throws Exceptions\TransportException
     */
    protected function createTransport( $transport = null ) {

        if ( !$transport instanceof TransportInterface ) {

            if ( is_string( $transport ) ) {

                $_transport = new $transport();

                if ( !$_transport instanceof TransportInterface ) {
                    throw New TransportException( $transport . " is not a valid TransportInterface instance" );
                }

            } else {
                //override with default
                $_transport = new SocketTransport();
            }

            $_transport->configure( array(
                    'hostname' => $this->hostname,
                    'port'     => $this->port,
                    'username' => $this->username,
                    'password' => $this->password
            ) );

        } else {
            $_transport = $transport;
        }

        return $_transport;
    }

    /**
     * Execute the given operation.
     *
     * @param string $operation The name of the operation to prepare.
     * @param array  $params    The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute( $operation, Array $params = array() ) {
        return $this->getTransport()->execute( $operation, $params );
    }

    /**
     * This is the first operation requested by the client when<br />
     * it needs to work with the server instance.<br />
     * It returns the session id of the client.
     *
     * @param array $params
     *
     * @return int
     */
    public function connect( Array $params = array() ) {
        return $this->getTransport()->execute( 'dataClusterAdd', $params );
    }

    /**
     * Execute a not idempotent SQL command
     *
     * @see PhpOrient\Protocols\Binary\Operations\Command
     * @param string $query
     * @return mixed
     */
    public function command( $query = '' ){
        $params = [];
        return $this->getTransport()->execute( 'recordUpdate',
                $params['command'] = Constants::QUERY_CMD,
                $params[ 'query' ] = $query
        );
    }

    /**
     * Execute an idempotent SQL command ( Select usually )
     *
     * @see PhpOrient\Protocols\Binary\Operations\Command
     *
     * @param string $query
     * @param int $limit
     * @param string $fetchPlan
     * @return mixed
     */
    public function query( $query = '', $limit = 20, $fetchPlan = '*:0' ){
        $params = [];
        return $this->getTransport()->execute( 'command',
                $params[ 'command' ]    = Constants::QUERY_SYNC,
                $params[ 'query' ]      = $query,
                $params[ 'limit' ]      = $limit,
                $params[ 'fetch_plan' ] = $fetchPlan
        );
    }

    /**
     * Execute an idempotent SQL command in Async mode( Select usually )<br />
     * A callback function is needed
     *
     * @see PhpOrient\Protocols\Binary\Operations\Command
     * @param string $query
     * @param int $limit
     * @param string $fetchPlan
     * @return mixed
     */
    public function queryAsync( $query = '', $limit = 20, $fetchPlan = '*:0' ){
        $params = [];
        return $this->getTransport()->execute( 'command',
                $params['command']      = Constants::QUERY_ASYNC,
                $params[ 'query' ]      = $query,
                $params[ 'limit' ]      = $limit,
                $params[ 'fetch_plan' ] = $fetchPlan
        );
    }

    /**
     * Execute an SQL Batch Script command<br />
     *
     * @see PhpOrient\Protocols\Binary\Operations\Command
     * @param string $param
     * @return mixed
     */
    public function sqlBatch( $param = '' ){
        $params = [];
        return $this->getTransport()->execute( 'command',
                $params['command'] = Constants::QUERY_SCRIPT,
                $params['query']   = $param
        );
    }

    /**
     * Update a Record
     *
     * @param array $params
     * @return RecordUpdate|Record
     */
    public function recordUpdate( Array $params = array() ){
        return $this->getTransport()->execute( 'recordUpdate', $params );
    }

    /**
     * Create a record
     *
     * @param array $params
     * @return RecordCreate|Record
     */
    public function recordCreate( Array $params = array() ){
        return $this->getTransport()->execute( 'recordCreate', $params );
    }

    /**
     * Delete a Record
     *
     * @param array $params
     * @return RecordDelete|Record
     */
    public function recordDelete( Array $params = array() ){
        return $this->getTransport()->execute( 'recordDelete', $params );
    }

    /**
     * Load a Record
     *
     * @param array $params
     * @return RecordLoad|Record
     */
    public function recordLoad( Array $params = array() ){
        return $this->getTransport()->execute( 'recordLoad', $params );
    }

    /**
     * Get the size of a Database
     *
     * @param array $params
     * @return int|string
     */
    public function dbSize( Array $params = array() ){
        return $this->getTransport()->execute( 'dbSize', $params );
    }

    /**
     * Reload the structure of a Database
     *
     * @param array $params
     * @return ClusterMap
     */
    public function dbReload( Array $params = array() ){
        return $this->getTransport()->execute( 'dbReload', $params );
    }

    /**
     * Release the structure of a Database
     *
     * @param array $params
     * @return bool
     */
    public function dbRelease( Array $params = array() ){
        return $this->getTransport()->execute( 'dbRelease', $params );
    }

    /**
     * Open a Database and perform a connection<br />
     * if it is not established before
     *
     * @param array $params
     *
     * @return ClusterMap
     */
    public function dbOpen( Array $params = array() ) {
        return $this->getTransport()->execute( 'dbOpen', $params );
    }

    /**
     * List all databases inside OrientDB instance
     *
     * @param array $params
     *
     * @return array
     */
    public function dbList( Array $params = array() ) {
        return $this->getTransport()->execute( 'dbList', $params );
    }

    /**
     * Freeze a database ( need Release to unlock )<br />
     * Flushes all cached content to the disk storage and allows to perform only read commands
     *
     * @see http://www.orientechnologies.com/docs/last/orientdb.wiki/Console-Command-Freeze-Db.html
     *
     * @param array $params
     *
     * @return bool
     */
    public function dbFreeze( Array $params = array() ) {
        return $this->getTransport()->execute( 'dbFreeze', $params );
    }

    /**
     * Check if a database exists
     *
     * @param array $params
     *
     * @return bool
     */
    public function dbExists( Array $params = array() ) {
        return $this->getTransport()->execute( 'dbExists', $params );
    }

    /**
     * Check if a database exists
     *
     * @param array $params
     *
     * @return bool
     */
    public function dbDrop( Array $params = array() ) {
        return $this->getTransport()->execute( 'dbDrop', $params );
    }

    /**
     * Create a new Database
     *
     * @param array $params
     *
     * @return bool
     */
    public function dbCreate( Array $params = array() ) {
        return $this->getTransport()->execute( 'dbCreate', $params );
    }

    /**
     * Create a new Database
     *
     * @param array $params
     *
     * @return int|string numeric
     */
    public function dbCountRecords( Array $params = array() ) {
        return $this->getTransport()->execute( 'dbCountRecords', $params );
    }

    /**
     * Close a database a drop the connection
     *
     * @param array $params
     *
     * @return int
     */
    public function dbClose( Array $params = array() ) {
        return $this->getTransport()->execute( 'dbClose', $params );
    }

    /**
     * Drop a data cluster
     *
     * @param array $params
     *
     * @return boolean True if the DataCluster was immediately deleted.
     */
    public function dataClusterDrop( Array $params = array() ) {
        return $this->getTransport()->execute( 'dataClusterDrop', $params );
    }

    /**
     * Returns the range of record ids for a cluster.
     *
     * @param array $params
     *
     * @return int[]|string[] numeric
     */
    public function dataClusterDataRange( Array $params = array() ) {
        return $this->getTransport()->execute( 'dataClusterDataRange', $params );
    }

    /**
     * Returns the number of records in one or more clusters.
     *
     * @param array $params
     *
     * @return int|string numeric
     */
    public function dataClusterCount( Array $params = array() ) {
        return $this->getTransport()->execute( 'dataClusterCount', $params );
    }

    /**
     * Add a new data cluster
     *
     * @param array $params
     *
     * @return int
     */
    public function dataClusterAdd( Array $params = array() ) {
        return $this->getTransport()->execute( 'dataClusterAdd', $params );
    }


}
