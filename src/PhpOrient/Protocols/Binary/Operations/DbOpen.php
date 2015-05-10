<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Exceptions\PhpOrientBadMethodCallException;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Configuration\Constants as ClientConstants;
use PhpOrient\Protocols\Common\ClusterMap;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Exceptions\PhpOrientWrongProtocolVersionException;

class DbOpen extends Operation {

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::DB_OPEN_OP;

    /**
     * @var string the name of the client library.
     */
    public $clientName = ClientConstants::NAME;

    /**
     * @var string the client version.
     */
    public $clientVersion = ClientConstants::VERSION;

    /**
     * @var string Client identifier
     */
    protected $_clientID = ClientConstants::ID; //not used

    /**
     * Type of serialization
     * @var string
     */
    public $serializationType = Constants::SERIALIZATION_DOCUMENT2CSV;

    /**
     * @var string the name of the database to open.
     */
    public $database;

    /**
     * @var string The database_type of database to open.
     */
    public $type = Constants::DATABASE_TYPE_GRAPH;

    /**
     * @var string the username to connect with.
     */
    public $username;

    /**
     * @var string the password to connect with.
     */
    public $password;

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        if( $this->serializationType == Constants::SERIALIZATION_SERIAL_BIN ){
            throw new PhpOrientWrongProtocolVersionException( 'Serialization Type Binary not yet supported' );
        }

        if( empty( $this->username ) && empty( $this->password ) ){
            throw new PhpOrientBadMethodCallException('Can not open a database ' .
                'without login parameters');
        }

        $this->_writeString( $this->clientName );
        $this->_writeString( $this->clientVersion );
        $this->_writeShort( $this->_transport->getProtocolVersion() );

        if( $this->_transport->getProtocolVersion() > 21 ){
            $this->_writeString( $this->_clientID ); // client id, unused.
            $this->_writeString( $this->serializationType ); // serialization database_type

            if( $this->_transport->getProtocolVersion() > 26 ){
                $this->_writeBoolean( $this->_transport->isRequestToken() ); # token
            }

            $this->_writeString( $this->database );
            $this->_writeString( $this->type );
            $this->_writeString( $this->username );
            $this->_writeString( $this->password );
        } else {
            $this->_writeString( $this->_clientID ); // client id, unused.
            $this->_writeString( $this->database );
            $this->_writeString( $this->type );
            $this->_writeString( $this->username );
            $this->_writeString( $this->password );
        }

    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function _read() {
        $sessionId = $this->_readInt();
        $this->_transport->setSessionId( $sessionId );
        $this->_transport->databaseOpened = true;
        $this->_transport->connected = false;

        if ( $this->_transport->getProtocolVersion() > 26 ) {
            $token = $this->_readString(); # token
            if( empty( $token ) ){
                $this->_transport->setRequestToken( false );
            }
            $this->_transport->setToken( $token );
        }

        $totalClusters = $this->_readShort();

        $dataClusters      = [ ];
        for ( $i = 0; $i < $totalClusters; $i++ ) {

            if( $this->_transport->getProtocolVersion() < 24 ){

                $dataClusters[ ] = [
                        'name'        => $this->_readString(),
                        'id'          => $this->_readShort(),
                        'type'        => $this->_readString(),
                        'dataSegment' => $this->_readShort()
                ];

            } else {
                $dataClusters[ ] = [
                        'name'        => $this->_readString(),
                        'id'          => $this->_readShort(),
                ];
            }

        }

        # cluster config string ( -1 )
        # cluster release
        $cluster_list = [
            'sessionId'    => $sessionId,
            'dataClusters' => $dataClusters,
            'servers'      => $this->_readString(),
            'release'      => $this->_readString()
        ];

        $this->_transport->setClusterMap( ClusterMap::fromConfig( $cluster_list ) );

        return $this->_transport->getClusterMap();

    }

}
