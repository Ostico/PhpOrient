<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Configuration\Constants as ClientConstants;

class Connect extends Operation {

    /**
     * @var string Client identifier
     */
    protected $_clientID = ClientConstants::ID; //not used

    /**
     * @var int The op code.
     */
    public $opCode = Constants::CONNECT_OP;

    /**
     * @var string the name of the client library.
     */
    public $clientName = ClientConstants::NAME;

    /**
     * @var string the client version.
     */
    public $clientVersion = ClientConstants::VERSION;

    /**
     * @var string the serialization type
     */
    public $serializationType = Constants::SERIALIZATION_DOCUMENT2CSV;

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

        if( $this->protocolVersion > 21 ) {

            $this->writeString( $this->_clientID );
            $this->writeString( $this->serializationType );
            $this->writeString( $this->username );
            $this->writeString( $this->password );

        } else {

            $this->writeString( $this->_clientID );
            $this->writeString( $this->username );
            $this->writeString( $this->password );

        }

    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function read() {

        $this->sessionId = $this->readInt();
        return $this->sessionId;

    }

}
