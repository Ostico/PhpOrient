<?php

namespace PhpOrient\Protocols\Binary;

use PhpOrient\Configuration\Constants as ClientConstants;
use \PhpOrient\Exceptions\SocketException;
use PhpOrient\Exceptions\PhpOrientWrongProtocolVersionException;
use PhpOrient\Protocols\Binary\Stream\Reader;


class OrientSocket {

    /**
     * @var bool Whether the protocol has been connected yet.
     */
    public $connected = false;

    /**
     * @var int the maximum known protocol version
     */
    public $protocolVersion = ClientConstants::SUPPORTED_PROTOCOL;

    /**
     * The socket resource
     *
     * @var resource OrientSocket
     */
    public $_socket;

    /**
     * Server host address
     *
     * @var string
     */
    public $hostname = '';

    /**
     * Server port
     * @var int
     */
    public $port = -1;

    const CONN_TIMEOUT = 5;
    const READ_TIMEOUT = 30;
    const WRITE_TIMEOUT = 10;

    /**
     * Create and open the socket.
     *
     * @param string $hostname   The host or IP address to connect to.
     * @param int    $port       The remote port.
     *
     * @throws SocketException If the socket cannot be opened.
     * @throws PhpOrientWrongProtocolVersionException If the socket cannot be opened.
     */
    public function __construct( $hostname, $port ) {
        $this->hostname = $hostname;
        $this->port = $port;
    }

    /**
     * Gets the OrientSocket, and establishes the connection if required.
     *
     * @return \PhpOrient\Protocols\Binary\OrientSocket
     *
     * @throws SocketException
     * @throws PhpOrientWrongProtocolVersionException
     */
    public function connect() {

        if ( !$this->connected ) {

            if( empty($this->hostname) && empty($this->port) ){
                throw new SocketException('Can not initialize a connection ' .
                        'without connection parameters');
            }

            $this->_socket = @socket_create( AF_INET, SOCK_STREAM, getprotobyname('tcp') );
            socket_set_block( $this->_socket );
            socket_set_option( $this->_socket, SOL_SOCKET, SO_RCVTIMEO, array( 'sec' => self::READ_TIMEOUT, 'usec' => 0 ) );
            socket_set_option( $this->_socket, SOL_SOCKET, SO_SNDTIMEO, array( 'sec' => self::WRITE_TIMEOUT, 'usec' => 0 ) );

            $x = @socket_connect( $this->_socket, $this->hostname, $this->port );

            if ( ! is_resource( $this->_socket ) || $x === false ) {
                throw new SocketException ( $this->getErr() . PHP_EOL );
            }

            $protocol = Reader::unpackShort( $this->read( 2 ) );

            if( $protocol > $this->protocolVersion ){
                throw new PhpOrientWrongProtocolVersionException('Protocol version ' . $protocol . ' is not supported.');
            }

            //protocol handshake
            $this->protocolVersion = $protocol;
            $this->connected = true;

        }

        return $this;
    }

    /**
     * Get Error from socket resource
     * @return string
     */
    public function getErr(){
        return "Error " . socket_last_error() . " : " . socket_strerror( socket_last_error() ) . "\n";
    }

    /**
     * Destroy the socket.
     */
    public function __destruct() {
        $this->protocolVersion = -1;
        $this->connected = false;
        @socket_close( $this->_socket );
        $this->_socket = null;
    }

    /**
     * Read a number of bytes from the socket.
     *
     * @param int $size The number of bytes to read, defaults to the socket's bufferSize.
     *
     * @return string The bytes read.
     * @throws SocketException
     */
    public function read( $size ) {

        $data      = '';
        $remaining = $size;

        do {
            $data .= socket_read( $this->_socket, $remaining, PHP_BINARY_READ );
            $remaining = $size - strlen( $data );
            if( $data === false || $data === '' ) {
                //https://bugs.php.net/bug.php?id=69008
                //I must hard-code the error because of a bug in PHP
                throw new SocketException( "socket_read(): unable to read from socket [104]: Connection reset by peer" );
            }
        } while ( $remaining > 0 );

        return $data;
    }

    /**
     * Write some bytes to the socket.
     *
     * @param mixed $bytes the bytes to write to the socket.
     * @throws SocketException
     *
     */
    public function write( $bytes ) {

        $lenOut = strlen( $bytes );
        $bytesWritten = socket_write( $this->_socket, $bytes, $lenOut );
        if( $bytesWritten === false ){
            $this->__destruct();
            throw new SocketException( $this->getErr() . " - socket_write() failed. Bytes Expected to Write: " . $lenOut . " - Written :" . intval($bytesWritten) );
        }

    }

}
