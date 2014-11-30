<?php

namespace PhpOrient\Protocols\Binary\Abstracts;

use PhpOrient\Protocols\Binary\Stream\Reader;
use PhpOrient\Protocols\Binary\Stream\Writer;
use PhpOrient\Configuration\ConfigurableInterface;
use PhpOrient\Configuration\ConfigurableTrait;
use PhpOrient\Exceptions\SocketException;
use PhpOrient\Exceptions\PhpOrientException;
use PhpOrient\Protocols\Binary\Socket;

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
     * @var Socket The socket to write to.
     */
    public $socket;

    /**
     * Write the data to the socket.
     */
    abstract protected function write();

    /**
     * Read the response from the socket.
     *
     * @return mixed the response.
     */
    abstract protected function read();

    /**
     * Write the request header.
     */
    protected function writeHeader() {
        $this->writeByte( $this->opCode );
        $this->writeInt( $this->sessionId );
    }

    /**
     * Read the response header.
     *
     * @throws \PhpOrient\Exceptions\Exception if the response indicates an error.
     */
    protected function readHeader() {
        $status          = $this->readByte();
        $this->sessionId = $this->readInt();
        if ( $status === 1 ) {
            $this->readByte(); // discard the first byte of the error
            $error = $this->readError();
            throw $error;
        }
    }

    /**
     * Execute the operation.
     *
     * @return mixed The response from the server.
     * @throws PhpOrientException
     */
    public function execute() {
        if ( !$this->socket->connected ) {
            $protocol = $this->readShort();

            if( $protocol > $this->protocolVersion ){
                throw new PhpOrientException('Protocol version ' . $protocol . 'is not supported.');
            }

            //protocol handshake
            $this->protocolVersion = $protocol;
            $this->socket->connected = true;
        }
        $this->writeHeader();
        $this->write();
        $this->readHeader();

        return $this->read();
    }

    /**
     * Write a byte to the socket.
     *
     * @param int $value
     */
    protected function writeByte( $value ) {
        $this->socket->write( Writer::packByte( $value ) );
    }

    /**
     * Read a byte from the socket.
     *
     * @return int the byte read
     */
    protected function readByte() {
        return Reader::unpackByte( $this->socket->read( 1 ) );
    }

    /**
     * Write a character to the socket.
     *
     * @param string $value
     */
    protected function writeChar( $value ) {
        $this->socket->write( Writer::packByte( ord( $value ) ) );
    }

    /**
     * Read a character from the socket.
     *
     * @return int the character read
     */
    protected function readChar() {
        return chr( Reader::unpackByte( $this->socket->read( 1 ) ) );
    }

    /**
     * Write a boolean to the socket.
     *
     * @param bool $value
     */
    protected function writeBoolean( $value ) {
        $this->socket->write( Writer::packByte( (bool)$value ) );
    }

    /**
     * Read a boolean from the socket.
     *
     * @return bool the boolean read
     */
    protected function readBoolean() {
        $value = $this->socket->read( 1 );

        return (bool)Reader::unpackByte( $value );
    }

    /**
     * Write a short to the socket.
     *
     * @param int $value
     */
    protected function writeShort( $value ) {
        $this->socket->write( Writer::packShort( $value ) );
    }

    /**
     * Read a short from the socket.
     *
     * @return int the short read
     */
    protected function readShort() {
        return Reader::unpackShort( $this->socket->read( 2 ) );
    }

    /**
     * Write an integer to the socket.
     *
     * @param int $value
     */
    protected function writeInt( $value ) {
        $this->socket->write( Writer::packInt( $value ) );
    }

    /**
     * Read an integer from the socket.
     *
     * @return int the integer read
     */
    protected function readInt() {
        return Reader::unpackInt( $this->socket->read( 4 ) );
    }


    /**
     * Write a long to the socket.
     *
     * @param int $value
     */
    protected function writeLong( $value ) {
        $this->socket->write( Writer::packLong( $value ) );
    }

    /**
     * Read a long from the socket.
     *
     * @return int the integer read
     */
    protected function readLong() {
        return Reader::unpackLong( $this->socket->read( 8 ) );
    }

    /**
     * Write a string to the socket.
     *
     * @param string $value
     */
    protected function writeString( $value ) {
        $this->socket->write( Writer::packString( $value ) );
    }

    /**
     * Read a string from the socket.
     *
     * @return string|null the string read, or null if it's empty.
     */
    protected function readString() {
        $length = $this->readInt();
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
    protected function writeBytes( $value ) {
        $this->socket->write( Writer::packBytes( $value ) );
    }

    /**
     * Read bytes from the socket.
     *
     * @return string|null the bytes read, or null if it's empty.
     */
    protected function readBytes() {
        $length = $this->readInt();
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
     * @return SocketException the wrapped exception object.
     */
    protected function readError() {
        $type    = $this->readString();
        $message = $this->readString();
        $hasMore = $this->readByte();
        if ( $hasMore === 1 ) {
            $next = $this->readError();
        } else {
            $javaStackTrace = $this->readBytes();
        }

        return new SocketException( $type . ': ' . $message );
    }

    /**
     * Read a serialized object from the remote server.
     *
     * @return mixed
     */
    protected function readSerialized() {
        $serialized = $this->readString();

        return Deserializer::deserialize( $serialized );
    }

    /**
     * Read a record from the remote server.
     *
     * @return array
     * @throws SocketException
     */
    protected function readRecord() {
        $classId = $this->readShort();
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
                    $record[ 'cluster' ]  = $this->readShort();
                    $record[ 'position' ] = $this->readLong();
                } else {
                    $record[ 'type' ]     = $this->readChar();
                    $record[ 'cluster' ]  = $this->readShort();
                    $record[ 'position' ] = $this->readLong();
                    $record[ 'version' ]  = $this->readInt();
                    $record[ 'bytes' ]    = $this->readBytes();
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
    protected function readCollection() {
        $records = [ ];
        $total   = $this->readInt();
        for ( $i = 0; $i < $total; $i++ ) {
            $records[ ] = $this->readRecord();
        }

        return $records;
    }


}
