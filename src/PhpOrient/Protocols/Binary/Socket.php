<?php

namespace PhpOrient\Protocols\Binary;

use PhpOrient\Configuration\Constants;
use \PhpOrient\Exceptions\SocketException;
use PhpOrient\Exceptions\PhpOrientWrongProtocolVersionException;
use PhpOrient\Protocols\Binary\Stream\Reader;

class Socket {

    /**
     * @var bool Whether the protocol has been connected yet.
     */
    public $connected = false;

    /**
     * @var int the maximum known protocol version
     */
    public $protocolVersion = Constants::SUPPORTED_PROTOCOL;

    /**
     * The socket resource
     *
     * @var resource Socket
     */
    protected static $_socket;

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

        self::$_socket = @socket_create( AF_INET, SOCK_STREAM, getprotobyname('tcp') );
        socket_set_block( self::$_socket );
        socket_set_option( self::$_socket, SOCK_STREAM, SO_RCVTIMEO, array( 'sec' => self::READ_TIMEOUT, 'usec' => 0 ) );
        socket_set_option( self::$_socket, SOCK_STREAM, SO_SNDTIMEO, array( 'sec' => self::WRITE_TIMEOUT, 'usec' => 0 ) );

        $x = @socket_connect( self::$_socket, $hostname, $port );

        if ( ! is_resource( self::$_socket ) || $x === false ) {
            throw new SocketException ( $this->getErr() . PHP_EOL );
        }

        $protocol = Reader::unpackShort( $this->read( 2 ) );

        if( $protocol > $this->protocolVersion ){
            throw new PhpOrientWrongProtocolVersionException('Protocol version ' . $protocol . 'is not supported.');
        }

        //protocol handshake
        $this->protocolVersion = $protocol;
        $this->connected = true;

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
        @socket_close( self::$_socket );
        self::$_socket = null;
    }

    /**
     * Read a number of bytes from the socket.
     *
     * @param int $size The number of bytes to read, defaults to the socket's bufferSize.
     *
     * @return string The bytes read.
     */
    public function read( $size ) {

        $data      = '';
        $remaining = $size;

        do {
            $data .= socket_read( self::$_socket, $remaining, PHP_BINARY_READ );
            $remaining = $size - strlen( $data );
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
        $bytesWritten = socket_write( self::$_socket, $bytes, $lenOut );
        if( $bytesWritten === false ){
            $this->__destruct();
            throw new SocketException( $this->getErr() . " - socket_write() failed. Bytes Expected to Write: " . $lenOut . " - Written :" . intval($bytesWritten) );
        }

    }
}
