<?php

namespace PhpOrient\Protocols\Binary;

use \PhpOrient\Exceptions\SocketException as SocketException;

class Socket {

    /**
     * @var bool Whether the protocol has been connected yet.
     */
    public $connected = false;

    /**
     * The raw socket.
     *
     * @var resource
     */
    protected $rawSocket;

    /**
     * @var int The number of bytes to buffer.
     */
    protected $bufferSize;


    /**
     * Create and open the socket.
     *
     * @param string $hostname   The host or IP address to connect to.
     * @param int    $port       The remote port.
     * @param int    $timeout    The number of seconds before timeout, defaults to 5.
     * @param int    $bufferSize The number of bytes to buffer.
     *
     * @throws SocketException If the socket cannot be opened.
     */
    public function __construct( $hostname, $port, $timeout = 5, $bufferSize = 4096 ) {
        $this->rawSocket = @fsockopen( $hostname, $port, $errNumber, $errMessage, $timeout );
        if ( $this->rawSocket === false ) {
            throw new SocketException( $errMessage, $errNumber );
        }
        stream_set_blocking( $this->rawSocket, 1 );
        stream_set_timeout( $this->rawSocket, 1 );
        $this->bufferSize = $bufferSize;
    }

    /**
     * Destroy the socket.
     */
    public function __destruct() {
        fclose( $this->rawSocket );
    }

    /**
     * Read a number of bytes from the socket.
     *
     * @param int $size The number of bytes to read, defaults to the socket's bufferSize.
     *
     * @return string The bytes read.
     */
    public function read( $size = null ) {
        if ( $size === null ) {
            return fread( $this->rawSocket, $this->bufferSize );
        }
        $data      = '';
        $remaining = $size;
        do {
            $data .= fread( $this->rawSocket, $remaining );
            $remaining = $size - strlen( $data );
        } while ( $remaining > 0 );

        return $data;
    }

    /**
     * Write some bytes to the socket.
     *
     * @param mixed $bytes the bytes to write to the socket.
     */
    public function write( $bytes ) {
        fwrite( $this->rawSocket, $bytes );
    }
}
