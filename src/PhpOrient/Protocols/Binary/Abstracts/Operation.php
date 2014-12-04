<?php

namespace PhpOrient\Protocols\Binary\Abstracts;

use PhpOrient\Protocols\Binary\Stream\Reader;
use PhpOrient\Protocols\Binary\Stream\Writer;
use PhpOrient\Protocols\Common\ConfigurableInterface;
use PhpOrient\Protocols\Common\ConfigurableTrait;
use PhpOrient\Exceptions\SocketException;
use PhpOrient\Exceptions\PhpOrientException;
use PhpOrient\Protocols\Binary\OrientSocket;

use PhpOrient\Configuration\Constants as ClientConstants;

abstract class Operation implements ConfigurableInterface {
    use ConfigurableTrait;

    /**
     * @var int the maximum known protocol version
     */
    public $protocolVersion = ClientConstants::SUPPORTED_PROTOCOL;

    /**
     * @var int The op code.
     */
    public $opCode;

    /**
     * @var int The session id, if any.
     */
    public $sessionId = -1;


    /**
     * @var OrientSocket The socket to write to.
     */
    public $socket;

    /**
     * Stack of elements to compile
     *
     * @var array
     */
    protected $_writeStack = array();

    /**
     * If transaction started
     *
     * @var bool
     */
    public $inTransaction = false;

    /**
     * Write the data to the socket.
     */
    abstract protected function _write();

    /**
     * Read the response from the socket.
     *
     * @return mixed the response.
     */
    abstract protected function _read();

    /**
     * Class constructor
     *
     * @param OrientSocket $socket
     */
    public function __construct( OrientSocket $socket ) {

        $socket->connect();

        $this->socket            = $socket;
        $this->protocolVersion   = $socket->protocolVersion;
        $this->sessionId         = $socket->sessionID;

    }

    /**
     * Write the request header.
     */
    protected function _writeHeader() {
        $this->_writeByte( $this->opCode );
        $this->_writeInt( $this->sessionId );
    }

    /**
     * Read the response header.
     *
     * @throws \PhpOrient\Exceptions\SocketException if the response indicates an error.
     */
    protected function _readHeader() {
        $status          = $this->_readByte();
        $this->sessionId = $this->_readInt();
        if ( $status === 1 ) {
            $this->_readByte(); // discard the first byte of the error
            $error = $this->_readError();
            throw $error;
        }
    }

    /**
     * Build the operation payload
     *
     * @return $this
     * @throws PhpOrientException
     */
    public function prepare() {
        $this->_writeHeader();
        $this->_write();
        return $this;
    }

    /**
     * Send message to orient server
     *
     * @return $this
     *
     * @throws SocketException
     */
    public function send(){
        $this->socket->write( implode( "", $this->_writeStack ) );
        return $this;
    }

    /**
     * Get Response from Server
     *
     * @return mixed
     * @throws PhpOrientException
     */
    public function getResponse(){
        $this->_readHeader();
        return $this->_read();
    }

    /**
     * Write a byte to the socket.
     *
     * @param int $value
     */
    protected function _writeByte( $value ) {
        $this->_writeStack[] = Writer::packByte( $value );
    }

    /**
     * Read a byte from the socket.
     *
     * @return int the byte read
     */
    protected function _readByte() {
        return Reader::unpackByte( $this->socket->read( 1 ) );
    }

    /**
     * Write a character to the socket.
     *
     * @param string $value
     */
    protected function _writeChar( $value ) {
        $this->_writeStack[] =  Writer::packByte( ord( $value ) ) ;
    }

    /**
     * Read a character from the socket.
     *
     * @return int the character read
     */
    protected function _readChar() {
        return chr( Reader::unpackByte( $this->socket->read( 1 ) ) );
    }

    /**
     * Write a boolean to the socket.
     *
     * @param bool $value
     */
    protected function _writeBoolean( $value ) {
        $this->_writeStack[] =  Writer::packByte( (bool)$value ) ;
    }

    /**
     * Read a boolean from the socket.
     *
     * @return bool the boolean read
     */
    protected function _readBoolean() {
        $value = $this->socket->read( 1 );
        return (bool)Reader::unpackByte( $value );
    }

    /**
     * Write a short to the socket.
     *
     * @param int $value
     */
    protected function _writeShort( $value ) {
        $this->_writeStack[] =  Writer::packShort( $value ) ;
    }

    /**
     * Read a short from the socket.
     *
     * @return int the short read
     */
    protected function _readShort() {
        return Reader::unpackShort( $this->socket->read( 2 ) );
    }

    /**
     * Write an integer to the socket.
     *
     * @param int $value
     */
    protected function _writeInt( $value ) {
        $this->_writeStack[] =  Writer::packInt( $value ) ;
    }

    /**
     * Read an integer from the socket.
     *
     * @return int the integer read
     */
    protected function _readInt() {
        return Reader::unpackInt( $this->socket->read( 4 ) );
    }


    /**
     * Write a long to the socket.
     *
     * @param int $value
     */
    protected function _writeLong( $value ) {
        $this->_writeStack[] =  Writer::packLong( $value ) ;
    }

    /**
     * Read a long from the socket.
     *
     * @return int the integer read
     */
    protected function _readLong() {
        return Reader::unpackLong( $this->socket->read( 8 ) );
    }

    /**
     * Write a string to the socket.
     *
     * @param string $value
     */
    protected function _writeString( $value ) {
        $this->_writeStack[] =  Writer::packString( $value ) ;
    }

    /**
     * Read a string from the socket.
     *
     * @return string|null the string read, or null if it's empty.
     */
    protected function _readString() {
        $length = $this->_readInt();
        if ( $length === -1 ) {
            return null;
        } else {
            if ( $length === 0 ) {
                return '';
            } else {
                return $this->socket->read( $length );
            }
        }
    }

    /**
     * Write bytes to the socket.
     *
     * @param string $value
     */
    protected function _writeBytes( $value ) {
        $this->_writeStack[] =  Writer::packBytes( $value ) ;
    }

    /**
     * Read bytes from the socket.
     *
     * @return string|null the bytes read, or null if it's empty.
     */
    protected function _readBytes() {
        $length = $this->_readInt();
        if ( $length === -1 ) {
            return null;
        } else {
            if ( $length === 0 ) {
                return '';
            } else {
                return $this->socket->read( $length );
            }
        }
    }

    /**
     * Read an error from the remote server and turn it into an exception.
     *
     * @return PhpOrientException the wrapped exception object.
     */
    protected function _readError() {
        $type    = $this->_readString();
        $message = $this->_readString();
        $hasMore = $this->_readByte();
        if ( $hasMore === 1 ) {
            $next = $this->_readError();
        } else {
            $javaStackTrace = $this->_readBytes();
        }

        return new PhpOrientException( $type . ': ' . $message );
    }

    /**
     * Read a serialized object from the remote server.
     *
     * @return mixed
     */
    protected function _readSerialized() {
        $serialized = $this->_readString();

        return Deserializer::deserialize( $serialized );
    }

    /**
     * Read a record from the remote server.
     *
     * @return array
     * @throws SocketException
     */
    protected function _readRecord() {
        $classId = $this->_readShort();
        $record  = [ 'classId' => $classId ];

        if ( $classId === -1 ) {
            throw new SocketException( 'No class for record, cannot proceed!' );
        } else {
            if ( $classId === -2 ) {
                // null record
                $record[ 'bytes' ] = null;
            } else {
                if ( $classId === -3 ) {
                    // reference
                    $record[ 'type' ]     = 'd';
                    $record[ 'cluster' ]  = $this->_readShort();
                    $record[ 'position' ] = $this->_readLong();
                } else {
                    $record[ 'type' ]     = $this->_readChar();
                    $record[ 'cluster' ]  = $this->_readShort();
                    $record[ 'position' ] = $this->_readLong();
                    $record[ 'version' ]  = $this->_readInt();
                    $record[ 'bytes' ]    = $this->_readBytes();
                }
            }
        }

        return $record;
    }

    /**
     * Read a collection of records from the remote server.
     *
     * @return array
     */
    protected function _readCollection() {
        $records = [ ];
        $total   = $this->_readInt();
        for ( $i = 0; $i < $total; $i++ ) {
            $records[ ] = $this->_readRecord();
        }

        return $records;
    }


}
