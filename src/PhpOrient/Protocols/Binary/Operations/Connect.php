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
    protected function _write() {

        $this->_writeString( $this->clientName );
        $this->_writeString( $this->clientVersion );
        $this->_writeShort( $this->protocolVersion );

        if( $this->protocolVersion > 21 ) {

            $this->_writeString( $this->_clientID );
            $this->_writeString( $this->serializationType );
            $this->_writeString( $this->username );
            $this->_writeString( $this->password );

        } else {

            $this->_writeString( $this->_clientID );
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

        $this->sessionId = $this->_readInt();
        $this->socket->sessionID = $this->sessionId;
        return $this->sessionId;

    }

}
