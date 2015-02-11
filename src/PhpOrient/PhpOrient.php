<?php
/**
 * Client Class
 *
 */

namespace PhpOrient;

use PhpOrient\Protocols\Binary\Data\ID;
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
 * Class PhpOrient
 *
 * @package PhpOrient
 */
class PhpOrient implements ConfigurableInterface {
    use ConfigurableTrait;

    const DATABASE_TYPE_DOCUMENT     = Constants::DATABASE_TYPE_DOCUMENT;
    const DATABASE_TYPE_GRAPH        = Constants::DATABASE_TYPE_GRAPH;
    const CLUSTER_TYPE_PHYSICAL      = Constants::CLUSTER_TYPE_PHYSICAL;
    const CLUSTER_TYPE_MEMORY        = Constants::CLUSTER_TYPE_MEMORY;
    const SERIALIZATION_DOCUMENT2CSV = Constants::SERIALIZATION_DOCUMENT2CSV;
    const SERIALIZATION_SERIAL_BIN   = Constants::SERIALIZATION_SERIAL_BIN;
    const STORAGE_TYPE_LOCAL         = Constants::STORAGE_TYPE_LOCAL;
    const STORAGE_TYPE_PLOCAL        = Constants::STORAGE_TYPE_PLOCAL;
    const STORAGE_TYPE_MEMORY        = Constants::STORAGE_TYPE_MEMORY;

    /**
     * The server host.
     *
     * @var string
     */
    public $hostname;

    /**
     * The port for the server.
     *
     * @var string
     */
    public $port;

    /**
     * The username for the server.
     *
     * @var string
     */
    public $username;

    /**
     * The password for the server.
     *
     * @var string
     */
    public $password;

    /**
     * The transport to use for the connection to the server.
     *
     * @var TransportInterface
     */
    protected $_transport;

    /**
     * Class Constructor
     *
     * @param string $hostname The server host.
     * @param string $port The server port.
     * @param string|bool $token An old connection Token to reuse,
     *                           or a flag to set a new token instance initialization
     */
    public function __construct( $hostname = '', $port = '', $token = '' ) {
        if ( !empty( $hostname ) ) {
            $this->hostname = $hostname;
        }

        if ( !empty( $port ) ) {
            $this->port = $port;
        }

        $this->setSessionToken( $token );

    }

    /**
     * Set the session token to re-use old
     * connection credentials
     * or a flag to set a new token instance initialization
     *
     * @param string|bool $token
     *
     * @return PhpOrient
     */
    public function setSessionToken( $token = '' ){
        if ( !empty( $token ) ){
            if ( $token !== true ) {
                $this->getTransport()->setToken( $token );
            }
            $this->getTransport()->setRequestToken();
        }
        return $this;
    }

    /**
     * Get the token for this connection
     *
     * @return string
     */
    public function getSessionToken(){
        return $this->getTransport()->getToken();
    }

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
     * @return \PhpOrient\Protocols\Binary\SocketTransport
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
    public function getTransactionStatement() {
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
     *
     * This is the first operation requested by the client when<br />
     * it needs to work with the server instance.<br />
     * It returns the session id of the client.
     *
     * @param string $username
     * @param string $password
     * @param string $serializationType
     *
     * @return mixed
     */
    public function connect(
        $username = '',
        $password = '',
        $serializationType = Constants::SERIALIZATION_DOCUMENT2CSV ) {

        if ( !empty( $username ) ) {
            $params[ 'username' ] = $username;
        }

        if ( !empty( $password ) ) {
            $params[ 'password' ] = $password;
        }

        $params[ 'serializationType' ] = $serializationType;

        return $this->getTransport()->execute( 'connect', $params );
    }

    /**
     * Send a command to shutdown the server
     * Requires "shutdown" permission to be set in orientdb-server-config.xml file
     *
     * @param string $username
     * @param string $password
     */
    public function shutDown( $username = '', $password = '' ){
        $this->execute( 'shutDown', [ 'username' => $username, 'password' => $password ] );
    }

    /**
     * Execute a not idempotent SQL command
     *
     * @see PhpOrient\Protocols\Binary\Operations\Command
     *
     * @param string $query
     *
     * @return mixed
     */
    public function command( $query ) {
        $params              = [ ];
        $params[ 'command' ] = Constants::QUERY_CMD;
        $params[ 'query' ]   = $query;

        return $this->getTransport()->execute( 'command', $params );
    }

    /**
     * Execute an idempotent SQL command ( Select usually )
     *
     * @see PhpOrient\Protocols\Binary\Operations\Command
     *
     * @param string $query
     * @param int    $limit
     * @param string $fetchPlan
     *
     * @return mixed
     */
    public function query( $query, $limit = 20, $fetchPlan = '*:0' ) {
        $params                 = [ ];
        $params[ 'command' ]    = Constants::QUERY_SYNC;
        $params[ 'query' ]      = $query;
        $params[ 'limit' ]      = ( !stripos( $query, ' limit ' ) ? $limit : -1 );
        $params[ 'fetch_plan' ] = $fetchPlan;

        return $this->getTransport()->execute( 'command', $params );
    }

    /**
     * Execute an idempotent SQL command in Async mode( Select usually )<br />
     * A callback function is needed
     *
     * @see PhpOrient\Protocols\Binary\Operations\Command
     *
     * @param string $query
     * @param Array    $params
     *
     * @return mixed
     */
    public function queryAsync( $query, Array $params = array() ) {
        $params[ 'command' ]    = Constants::QUERY_ASYNC;
        $params[ 'query' ]      = $query;

        return $this->getTransport()->execute( 'command', $params );
    }

    /**
     * Execute an SQL Batch Script command<br />
     *
     * @see PhpOrient\Protocols\Binary\Operations\Command
     *
     * @param string $param
     *
     * @return mixed
     */
    public function sqlBatch( $param ) {
        $params              = [ ];
        $params[ 'command' ] = Constants::QUERY_SCRIPT;
        $params[ 'query' ]   = $param;

        return $this->getTransport()->execute( 'command', $params );
    }

    /**
     * Update a Record
     *
     * @param Record $record
     *
     * @return RecordUpdate|Record
     */
    public function recordUpdate( Record $record ) {
        return $this->getTransport()->execute( 'recordUpdate',
            [
                'rid'              => $record->getRid(),
                'record'           => $record,
                'record_version'   => $record->getVersion()
            ]
        );
    }

    /**
     * Create a record
     *
     * @param Record $record
     *
     * @return RecordCreate|Record
     */
    public function recordCreate(  Record $record ) {
        return $this->getTransport()->execute( 'recordCreate', [
            'cluster_id' => $record->getRid()->cluster,
            'record'     => $record
        ] );
    }

    /**
     * Delete a Record
     *
     * @param ID $rid
     *
     * @return RecordDelete|bool
     */
    public function recordDelete( ID $rid ) {
        return $this->getTransport()->execute( 'recordDelete', [
            'rid'    => $rid
        ] );
    }

    /**
     * Load a Record
     *
     * @param ID $rid
     * @param array $params
     *
     * @return RecordLoad/Record
     */
    public function recordLoad( ID $rid, Array $params = array()  ) {
        $params[ 'rid' ]      = $rid;
        return $this->getTransport()->execute( 'recordLoad', $params );
    }

    /**
     * Get the size of a Database
     *
     * @return int|string
     */
    public function dbSize() {
        return $this->getTransport()->execute( 'dbSize', [] );
    }

    /**
     * Reload the structure of a Database
     *
     * @return ClusterMap
     */
    public function dbReload() {
        return $this->getTransport()->execute( 'dbReload', [] );
    }

    /**
     * Release the structure of a Database
     *
     * @param string $db_name
     * @param string $storage_type
     *
     * @return true
     */
    public function dbRelease( $db_name, $storage_type = Constants::STORAGE_TYPE_PLOCAL ) {
        return $this->getTransport()->execute( 'dbRelease', [
            'database'     => $db_name,
            'storage_type' => $storage_type
        ] );
    }

    /**
     * Open a Database and perform a connection<br />
     * if it is not established before
     *
     * @param string $database
     * @param string $username
     * @param string $password
     * @param array  $params {<code>
     *    'serializationType' => PhpOrient::SERIALIZATION_DOCUMENT2CSV,
     *    'databaseType'      => PhpOrient::DATABASE_TYPE_GRAPH
     * }</code>
     * @return ClusterMap
     */
    public function dbOpen( $database, $username = '', $password = '', Array $params = [] ) {

        $default = [
            'databaseType'      => Constants::DATABASE_TYPE_GRAPH,
            'serializationType' => Constants::SERIALIZATION_DOCUMENT2CSV,
        ];

        $params = array_merge( $default, $params );

        return $this->getTransport()->execute( 'dbOpen',
            array(
                'database'          => $database,
                'type'              => $params['databaseType'],
                'username'          => $username,
                'password'          => $password,
                'serializationType' => $params['serializationType']
            )
        );
    }

    /**
     * List all databases inside OrientDB instance
     *
     * @return array
     */
    public function dbList() {
        return $this->getTransport()->execute( 'dbList', [] );
    }

    /**
     * Freeze a database ( need Release to unlock )<br />
     * Flushes all cached content to the disk storage and allows to perform only read commands
     *
     * @see http://www.orientechnologies.com/docs/last/orientdb.wiki/Console-Command-Freeze-Db.html
     *
     * @param string $db_name
     * @param string $storage_type
     *
     * @return bool
     */
    public function dbFreeze( $db_name, $storage_type = Constants::STORAGE_TYPE_PLOCAL ) {
        return $this->getTransport()->execute( 'dbFreeze', [
            'database'     => $db_name,
            'storage_type' => $storage_type
        ] );
    }

    /**
     * Check if a database exists
     *
     * @param        $database
     * @param string $database_type
     *
     * @return bool
     */
    public function dbExists( $database, $database_type = Constants::DATABASE_TYPE_GRAPH ) {
        return $this->getTransport()->execute( 'dbExists',
            array(
                'database'      => $database,
                'database_type' => $database_type
            )
        );
    }

    /**
     * Drop an existent database
     *
     * @param        $database
     * @param string $storage_type
     *
     * @return true
     */
    public function dbDrop( $database, $storage_type = Constants::STORAGE_TYPE_PLOCAL ) {
        return $this->getTransport()->execute( 'dbDrop',
            array(
                'database'     => $database,
                'storage_type' => $storage_type
            )
        );
    }

    /**
     * Create a new Database
     *
     * @param string $database
     * @param string $storage_type
     * @param string $database_type
     *
     * @return bool
     */
    public function dbCreate( $database,
                              $storage_type = Constants::STORAGE_TYPE_PLOCAL,
                              $database_type = Constants::DATABASE_TYPE_GRAPH ) {

        return $this->getTransport()->execute( 'dbCreate',
            array(
                'database'      => $database,
                'database_type' => $database_type,
                'storage_type'  => $storage_type
            )
        );

    }

    /**
     * Create a new Database
     *
     * @return int|string numeric
     */
    public function dbCountRecords() {
        return $this->getTransport()->execute( 'dbCountRecords', [] );
    }

    /**
     * Close a database a drop the connection
     *
     * @return int
     */
    public function dbClose() {
        return $this->getTransport()->execute( 'dbClose', [] );
    }

    /**
     * Drop a data cluster
     *
     * @param int $cluster_id
     *
     * @return boolean True if the DataCluster was immediately deleted.
     */
    public function dataClusterDrop( $cluster_id ) {
        return $this->getTransport()->execute( 'dataClusterDrop', [ 'id' => $cluster_id ] );
    }

    /**
     * Returns the range of record ids for a cluster.
     *
     * @param int $cluster_id
     *
     * @return int[]|string[] numeric
     */
    public function dataClusterDataRange( $cluster_id ) {
        return $this->getTransport()->execute( 'dataClusterDataRange', [ 'id' => $cluster_id ] );
    }

    /**
     * Returns the number of records in one or more clusters.
     *
     * @param int[]|string[] numeric $cluster_ids
     *
     * @return int|string numeric
     */
    public function dataClusterCount( Array $cluster_ids = array() ) {
        return $this->getTransport()->execute( 'dataClusterCount', [ 'ids' => $cluster_ids ] );
    }

    /**
     * Add a new data cluster
     *
     * @param        $cluster_name
     * @param string $cluster_type
     *
     * @return int the new cluster ID
     */
    public function dataClusterAdd( $cluster_name, $cluster_type = Constants::CLUSTER_TYPE_PHYSICAL ) {
        return $this->getTransport()->execute( 'dataClusterAdd', [
            'cluster_name' => $cluster_name,
            'cluster_type' => $cluster_type
        ] );
    }


}
