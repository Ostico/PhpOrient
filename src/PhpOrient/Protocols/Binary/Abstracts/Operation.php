<?php

namespace PhpOrient\Protocols\Binary\Abstracts;

use PhpOrient\Protocols\Binary\Serialization\CSV;
use PhpOrient\Protocols\Binary\SocketTransport;
use PhpOrient\Protocols\Binary\Stream\Reader;
use PhpOrient\Protocols\Binary\Stream\Writer;
use PhpOrient\Protocols\Common\ConfigurableInterface;
use PhpOrient\Protocols\Common\ConfigurableTrait;
use PhpOrient\Exceptions\SocketException;
use PhpOrient\Exceptions\PhpOrientException;
use PhpOrient\Exceptions\PyOrientBadMethodCallException;
use PhpOrient\Protocols\Binary\OrientSocket;
use Closure;

abstract class Operation implements ConfigurableInterface {
    use ConfigurableTrait;

    /**
     * @var int The op code.
     */
    protected $opCode;

    /**
     * @var OrientSocket The socket to write to.
     */
    protected $_socket;

    /**
     * Stack of elements to compile
     *
     * @var array
     */
    protected $_writeStack = array();

    /**
     * @var string of read stream
     */
    protected $_input_buffer;

    /**
     * @var string of read stream
     */
    protected $_output_buffer;

    /**
     * @var SocketTransport
     */
    protected $_transport;

    /**
     * @var Closure|string
     */
    public $_callback;

    /**
     * Class constructor
     *
     * @param SocketTransport $_transport
     *
     * @throws SocketException
     * @throws \PhpOrient\Exceptions\PhpOrientWrongProtocolVersionException
     */
    public function __construct( SocketTransport $_transport ) {

        $this->_transport = $_transport;
        $this->_socket    = $_transport->getSocket();

        $this->_callback = function(){};

    }

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
     * @param SocketTransport $transport
     *
     * @return null|void
     * @throws PhpOrientException
     */
    protected function _checkConditions( SocketTransport $transport ){}

    /**
     * Write the request header.
     */
    protected function _writeHeader() {
        $this->_writeByte( $this->opCode );
        $this->_writeInt( $this->_transport->getSessionId() );
    }

    /**
     * Read the response header.
     *
     * @throws \PhpOrient\Exceptions\SocketException if the response indicates an error.
     */
    protected function _readHeader() {
        $status    = $this->_readByte();
        $sessionId = $this->_readInt();
        if ( $status === 1 ) {
            $this->_readByte(); // discard the first byte of the error
            $error = $this->_readError();
            $this->_dump_streams();
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
        $this->_checkConditions( $this->_transport );
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
        $this->_output_buffer = implode( "", $this->_writeStack );
        $this->_dump_streams();
        $this->_socket->write( $this->_output_buffer );
        $this->_output_buffer = '';
        $this->_writeStack = [];
        return $this;
    }

    /**
     * Log of input/output stream
     */
    protected function _dump_streams(){

        if( strlen( $this->_output_buffer ) ){
            $this->_transport->debug("Request:");
            $this->_transport->hexDump( $this->_output_buffer );
        }

        if( strlen( $this->_input_buffer ) ){
            $this->_transport->debug("Response:");
            $this->_transport->hexDump( $this->_input_buffer );
        }

    }

    /**
     * Get Response from Server
     *
     * @return mixed
     * @throws PhpOrientException
     */
    public function getResponse( ){

//        if ( $_continue ){
//            $result = $this->_read();
//            $this->_dump_streams();
//        } else{
            $this->_readHeader();
            $result = $this->_read();
            $this->_dump_streams();
            $this->_input_buffer = '';
//        }

        return $result;

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
        $this->_input_buffer .= $_read = $this->_socket->read( 1 );
        return Reader::unpackByte( $_read );
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
        $this->_input_buffer .= $_read = $this->_socket->read( 1 );
        return chr( $_read );
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
        $this->_input_buffer .= $value = $this->_socket->read( 1 );
        return ord($value) == 1;
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
        $this->_input_buffer .= $_read = $this->_socket->read( 2 );
        return Reader::unpackShort( $_read );
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
        $this->_input_buffer .= $_read = $this->_socket->read( 4 );
        return Reader::unpackInt( $_read );
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
        $this->_input_buffer .= $_read = $this->_socket->read( 8 );
        return Reader::unpackLong( $_read );
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
                $this->_input_buffer .= $string = $this->_socket->read( $length );
                return $string;
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
                $this->_input_buffer .= $string = $this->_socket->read( $length );
                return $string;
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

        return CSV::unserialize( $serialized );
    }

    /**
     * The format depends if a RID is passed or an entire
     *   record with its content.
     *
     * In case of null record then -2 as short is passed.
     *
     * In case of RID -3 is passes as short and then the RID:
     *   (-3:short)(cluster-id:short)(cluster-position:long).
     *
     * In case of record:
     *   (0:short)(record-type:byte)(cluster-id:short)
     *   (cluster-position:long)(record-version:int)(record-content:bytes)
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
                    $record[ 'oData' ]    = CSV::unserialize( $this->_readBytes() );
                }
            }
        }

        return $record;
    }

    /**
     * Read pre-fetched Records
     *
     * @throws PyOrientBadMethodCallException
     * @throws SocketException
     */
    protected function _read_prefetch_record(){

        $status = $this->_readByte();
        while ( $status != 0 ){

            $payload = $this->_readRecord();

            /**
             * @var Closure|string $_callback
             */
            if( !is_callable( $this->_callback, true ) ){
                throw new PyOrientBadMethodCallException(
                    "'$this->_callback' is not a callable function"
                );
            }

            /**
            * async-result-type byte as trailing byte of a record can be:
            * 0: no records remain to be fetched
            * 1: a record is returned as a result set
            * 2: a record is returned as pre-fetched to be loaded in client's
            *       cache only. It's not part of the result set but the client
            *       knows that it's available for later access
            */
            if( $status == 1 ){
                #  a record is returned as a result set
                call_user_func( $this->_callback, $payload );
            } elseif( $status == 2 ){

                #  save in cache
                call_user_func( $this->_callback, $payload );
            }

            $status = $this->_readByte();
        }

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
