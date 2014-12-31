<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Exceptions\PhpOrientBadMethodCallException;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Configuration\Constants as ClientConstants;
use PhpOrient\Exceptions\PhpOrientWrongProtocolVersionException;

class Connect extends Operation {

    /**
     * @var string Client identifier
     */
    protected $_clientID = ClientConstants::ID; //not used

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::CONNECT_OP;

    /**
     * @var string the name of the client library.
     */
    public $clientName = ClientConstants::NAME;

    /**
     * @var string the client version.
     */
    public $clientVersion = ClientConstants::VERSION;

    /**
     * @var string the serialization database_type
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

        if( $this->serializationType == Constants::SERIALIZATION_SERIAL_BIN ){
            throw new PhpOrientWrongProtocolVersionException( 'Serialization Type Binary not yet supported' );
        }

        if( empty( $this->username ) && empty( $this->password ) ){
            throw new PhpOrientBadMethodCallException('Can not begin a connection ' .
                'without connection parameters');
        }

        $this->_writeString( $this->clientName );
        $this->_writeString( $this->clientVersion );
        $this->_writeShort( $this->_transport->getProtocolVersion() );

        if( $this->_transport->getProtocolVersion() > 21 ) {

            $this->_writeString( $this->_clientID );
            $this->_writeString( $this->serializationType );

            if( $this->_transport->getProtocolVersion() > 26 ){
                $this->_writeBoolean( $this->_transport->isRequestToken() ); # token
            }

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

        $_sessionId = $this->_readInt();
        $this->_transport->setSessionId( $_sessionId );

        if ( $this->_transport->getProtocolVersion() > 26 ) {
            $token = $this->_readString(); # token
            if( empty( $token ) ){
                $this->_transport->setRequestToken( false );
            }
            $this->_transport->setToken( $token );
        }

        $this->_transport->connected = true;
        return $_sessionId;

    }

}
