<?php

namespace PhpOrient\Protocols\Binary;

use PhpOrient\Configuration\Constants;
use PhpOrient\Exceptions\TransportException;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Operations\Connect;
use PhpOrient\Protocols\Binary\Operations\DbOpen;
use PhpOrient\Protocols\Common\AbstractTransport;

class SocketTransport extends AbstractTransport {

    /**
     * If a transaction started
     *
     * @var bool
     */
    public $inTransaction = false;

    /**
     * Flag needed to know if a database is opened or not
     *
     * @var boolean
     */
    public $databaseOpened = false;

    /**
     * Flag needed to know if connected to the server
     *
     * @var boolean
     */
    public $connected = false;

    /**
     * @var OrientSocket the connected socket.
     */
    protected $_socket;

    /**
     * @var int The session id for the connection.
     */
    protected $sessionId = -1;

    /**
     * @var string
     */
    protected $token = '';

    /**
     * With this flag a session with token is requested
     *
     * @var bool
     */
    protected $requestToken = false;

    /**
     * @var int The Protocol id for the connection.
     */
    protected $_protocolVersion;

    /**
     * Gets the version of negotiated protocol
     *
     * @return int Protocol Version
     */
    public function getProtocolVersion(){
        return $this->_protocolVersion;
    }

    /**
     * @param int $protocolVersion
     */
    public function setProtocolVersion( $protocolVersion ) {
        $this->_protocolVersion = $protocolVersion;
    }

    /**
     * Gets the session ID for current connection
     *
     * @return int Session
     */
    public function getSessionId(){
        return $this->sessionId;
    }

    /**
     * @param $sessionId
     * @return SocketTransport
     */
    public function setSessionId( $sessionId ){
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param string $token
     * @return SocketTransport
     */
    public function setToken( $token ) {
        $this->token = $token;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequestToken() {
        return $this->requestToken;
    }

    /**
     * Set the client to get and send the token
     *
     * @param boolean $requestToken
     * @return SocketTransport
     */
    public function setRequestToken( $requestToken = true ) {
        $this->requestToken = (bool)$requestToken;
        return $this;
    }

    /**
     * Gets the Socket, and establishes the connection if required.
     *
     * @return \PhpOrient\Protocols\Binary\OrientSocket
     */
    public function getSocket() {
        if ( $this->_socket === null ) {
            $this->_socket          = new OrientSocket( $this->hostname, $this->port );
            $this->_protocolVersion = $this->_socket->connect()->protocolVersion;
        }
        return $this->_socket;
    }

    /**
     * Execute the operation with the given name.
     *
     * @param string $operation The operation to prepare.
     * @param array  $params    The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute( $operation, array $params = array() ) {

        $op = $this->operationFactory( $operation, $params );
        $result = $op->prepare()->send()->getResponse();
        return $result;

    }

    /**
     * @param Operation|string $operation The operation name or instance.
     * @param array            $params    The parameters for the operation.
     *
     * @return Operation The operation instance.
     * @throws TransportException
     */
    protected function operationFactory( $operation, array $params ) {

        if ( !( $operation instanceof Operation ) ) {

            if ( !strstr( $operation, '\\' ) ) {
                $operation = 'PhpOrient\Protocols\Binary\Operations\\' . ucfirst( $operation );
            }

            $operation = new $operation( $this );

            /**
             * Used when we want initialize the transport
             * from client configuration params
             *
             */
            if( $operation instanceof DbOpen || $operation instanceof Connect ){

                if( empty( $params[ 'username' ] ) ){
                    $params[ 'username' ] = $this->username;
                }

                if( empty( $params[ 'password' ] ) ){
                    $params[ 'password' ] = $this->password;
                }

            }

        }

        $operation->configure( $params );

        return $operation;
    }

    /**
     * View any string as a hexDump.
     *
     * This is most commonly used to view binary data from streams
     * or sockets while debugging, but can be used to view any string
     * with non-viewable characters.
     *
     * @version     1.3.2
     * @author      Aidan Lister <aidan@php.net>
     * @author      Peter Waller <iridum@php.net>
     * @link        http://aidanlister.com/2004/04/viewing-binary-data-as-a-hexDump-in-php/
     *
     * @param       string  $data        The string to be dumped
     * @param       bool $htmlOutput  Set to false for non-HTML output
     * @param       bool    $uppercase   Set to true for uppercase hex
     *
     * @return string|null
     */
    public static function _hexDump( $data, $htmlOutput = false, $uppercase = true ) {
        // Init
        $hexi = '';
        $ascii = '';
        $dump = ( $htmlOutput === true) ? '<pre>' : '';
        $offset = 0;
        $len = strlen ( $data );

        // Upper or lower case hexadecimal
        $x = ($uppercase === false) ? 'x' : 'X';

        // Iterate string
        for($i = $j = 0; $i < $len; $i ++) {
            // Convert to hexadecimal
            $hexi .= sprintf ( "%02$x ", ord ( $data [$i] ) );

            // Replace non-viewable bytes with '.'
            if (ord ( $data [$i] ) >= 32) {
                $ascii .= ( $htmlOutput === true) ? htmlentities ( $data [$i] ) : $data [$i];
            } else {
                $ascii .= '.';
            }

            // Add extra column spacing
            if ($j === 7) {
                $hexi .= ' ';
                $ascii .= ' ';
            }

            // Add row
            if (++ $j === 16 || $i === $len - 1) {
                // Join the hexi / ascii output
                $dump .= sprintf ( "%04$x  %-49s  %s", $offset, $hexi, $ascii );

                // Reset vars
                $hexi = $ascii = '';
                $offset += 16;
                $j = 0;

                // Add newline
                if ($i !== $len - 1) {
                    $dump .= "\n";
                }
            }
        }

        // Finish dump
        $dump .= $htmlOutput === true ? '</pre>' : '';
        $dump .= "\n";

        // Output method
        return $dump;

    }

    /**
     * Dump data stream to HexDec format
     *
     * @param $message
     */
    public function hexDump( $message ){
        if( Constants::$LOGGING ){
            $_msg = self::_hexDump( $message );
            $rows = explode( "\n", $_msg );
            $this->_logger->debug( "\n" );
            foreach( $rows as $row ){
                $this->_logger->debug( $row );
            }
        }
    }

}
