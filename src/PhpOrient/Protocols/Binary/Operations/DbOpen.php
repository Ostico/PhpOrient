<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Configuration\Constants as ClientConstants;
use PhpOrient\Protocols\Common\Constants;

class DbOpen extends Operation {
    /**
     * @var int The op code.
     */
    public $opCode = Constants::DB_OPEN_OP;

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
     * @var int the maximum known protocol version
     */
    public $protocolVersion = ClientConstants::SUPPORTED_PROTOCOL;

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
     * @var string The type of database to open.
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

    public function send(){

        if( $this->sessionId < 0 ){
            $connection = new Connect( $this->socket );
            $connection->configure( array(
                'username' => $this->username,
                'password' => $this->password
            ) );
            $connection->prepare()->send()->getResponse();
        }

        return parent::send();
    }

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        $this->_writeString( $this->clientName );
        $this->_writeString( $this->clientVersion );
        $this->_writeShort( $this->protocolVersion );

        if( $this->protocolVersion > 21 ){
            $this->_writeString( $this->_clientID ); // client id, unused.
            $this->_writeString( $this->serializationType ); // serialization type
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
        $sessionId     = $this->_readInt();
        $totalClusters = $this->_readShort();

        $dataClusters      = [ ];
        for ( $i = 0; $i < $totalClusters; $i++ ) {

            if( $this->protocolVersion < 24 ){

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
        return [
                'sessionId'    => $sessionId,
                'dataClusters' => $dataClusters,
                'servers'      => $this->_readInt(),
                'release'      => $this->_readString()
        ];

    }

}
