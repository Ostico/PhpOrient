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

    /**
     * Write the data to the socket.
     */
    protected function write() {
        $this->writeString( $this->clientName );
        $this->writeString( $this->clientVersion );
        $this->writeShort( $this->protocolVersion );
        $this->writeString( $this->_clientID ); // client id, unused.
        $this->writeString( $this->database );
        $this->writeString( $this->type );
        $this->writeString( $this->username );
        $this->writeString( $this->password );
    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function read() {
        $sessionId     = $this->readInt();
        $totalClusters = $this->readShort();
        $clusters      = [ ];
        for ( $i = 0; $i < $totalClusters; $i++ ) {
            $clusters[ ] = [
                    'name'        => $this->readString(),
                    'id'          => $this->readShort(),
                    'type'        => $this->readString(),
                    'dataSegment' => $this->readShort()
            ];
        }

        return [
                'sessionId' => $sessionId,
                'clusters'  => $clusters,
                'servers'   => $this->readSerialized(),
                'release'   => $this->readString()

        ];
    }

}
