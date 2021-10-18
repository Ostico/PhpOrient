<?php

namespace PhpOrient\Protocols\Binary\Abstracts;

use PhpOrient\Configuration\Constants;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Binary\Operations\Connect;
use PhpOrient\Protocols\Binary\Operations\DbOpen;
use PhpOrient\Protocols\Binary\Serialization\CSV;
use PhpOrient\Protocols\Binary\SocketTransport;
use PhpOrient\Protocols\Binary\Stream\Reader;
use PhpOrient\Protocols\Binary\Stream\Writer;
use PhpOrient\Protocols\Common\ConfigurableInterface;
use PhpOrient\Protocols\Common\ConfigurableTrait;
use PhpOrient\Exceptions\SocketException;
use PhpOrient\Exceptions\PhpOrientException;
use PhpOrient\Exceptions\PhpOrientBadMethodCallException;
use PhpOrient\Protocols\Binary\OrientSocket;
use Closure;
use PhpOrient\Protocols\Common\OrientNode;

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
     * Callback function to apply on Async records when they are fetched
     *
     * @var Closure|string
     */
    protected $_callback;

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
        $token = $this->_transport->getToken();

        /*
         *  we must recognize dbOpen and Connect messages
         */
        if(
            !$this instanceof DbOpen &&
            !$this instanceof Connect &&
            $this->_transport->isRequestToken() ){
            $this->_writeString( $token );
        }
    }

    /**
     * Read the response header.
     *
     * @throws \PhpOrient\Exceptions\SocketException if the response indicates an error.
     */
    protected function _readHeader() {
        $status    = $this->_readByte();
        $sessionId = $this->_readInt();

        /*
         *  we must recognize dbOpen and Connect messages
         */
        if(
            !$this instanceof DbOpen &&
            !$this instanceof Connect &&
            $this->_transport->isRequestToken() ){
            $token_refresh = $this->_readString();
            if( !empty( $token_refresh ) ){
                $this->_transport->setToken( $token_refresh );
            }
        }

        if ( $status === 1 ) {
            $this->_readByte(); // discard the first byte of the error
            $error = $this->_readError();
            $this->_dump_streams();
            throw $error;
        } elseif( $status === 3 ){
            // server push data for nodes up/down update info,
            // needed for failover on cluster

            # Push notification, Node cluster changed
            #
            # BYTE (OChannelBinaryProtocol.PUSH_DATA);  # WRITE 3
            # INT (Integer.MIN_VALUE);  # SESSION ID = 2^-31
            #       80: \x50 Request Push 1 byte: Push command id
            # STRING $payload
            $this->_pushReceived(
                $this->_readByte(),
                CSV::unserialize( $this->_readString() )
            );

            $end_flag = $this->_readByte();
            # this flag can be set more than once
            while ( $end_flag == 3 ) {
                $this->_readInt();  # FAKE SESSION ID = 2^-31
                $this->_pushReceived(
                    $this->_readByte(),
                    CSV::unserialize( $this->_readString() )
                );
                $end_flag = $this->_readByte();
            }

            $sessionId = $this->_readInt(); //string termination

        }

    }

    /**
     * Default callback for received push Notices
     *
     * @param $command_id
     * @param $payload
     */
    protected function _pushReceived( $command_id, $payload ){
        # REQUEST_PUSH_RECORD	        79
        # REQUEST_PUSH_DISTRIB_CONFIG	80
        # REQUEST_PUSH_LIVE_QUERY	    81
        # TODO: this logic must stay within Messages class here I just want to receive
        # an object of something, like a new array of cluster.
        # We should register a callback and then execute it
        $list = [];
        if ( $command_id == 80 ){
            foreach( $payload['members'] as $node ){
                $list[] = new OrientNode( $node );
            }
            $this->_transport->setNodesList( $list ); # LIST WITH THE NEW CLUSTER CFG
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

        # skip execution in case of transaction
        if( $this->_transport->inTransaction ){
            return $this;
        }

        $this->_output_buffer = implode( "", $this->_writeStack );
        $this->_dump_streams();
        $this->_socket->write( $this->_output_buffer );
        $this->_output_buffer = '';
//        $this->_writeStack = [];
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

        # skip execution in case of transaction
        if( $this->_transport->inTransaction ){
            return $this;
        }

        $this->_readHeader();
        $result = $this->_read();
        $this->_dump_streams();
        $this->_input_buffer = '';

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
        return chr( Reader::unpackByte( $_read ) );
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
        } elseif ( $classId === -2 ) {
            // null record
            $record[ 'bytes' ] = null;
        } elseif ( $classId === -3 ) {
            // reference
            $record[ 'type' ] = 'd';
            $cluster          = $this->_readShort();
            $position         = $this->_readLong();
            $record[ 'rid' ]  = new ID( $cluster, $position );
        } else {
            $record[ 'type' ]    = $this->_readChar();
            $cluster             = $this->_readShort();
            $position            = $this->_readLong();
            $record[ 'version' ] = $this->_readInt();

            $data               = CSV::unserialize( $this->_readBytes() );
            $record[ 'rid' ]    = new ID( $cluster, $position );
            if( isset( $data['oClass'] ) ){
                $record[ 'oClass' ] = $data[ 'oClass' ];
                unset( $data[ 'oClass' ] );
            }

            $record[ 'oData' ] = $data;
        }

        return $record;
    }

    /**
     * Read pre-fetched and async Records
     *
     * @return Record[]
     * @throws PhpOrientBadMethodCallException
     * @throws SocketException
     */
    protected function _read_prefetch_record(){

        $resultSet = [];
        $status = $this->_readByte();
        while ( $status != 0 ){

            $payload = $this->_readRecord();

            $record = Record::fromConfig( $payload );

            /**
             * @var Closure|string $_callback
             */
            if( !is_callable( $this->_callback, true ) ){
                throw new PhpOrientBadMethodCallException(
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
                $resultSet[] = $record;
            } elseif( $status == 2 ){

                #  save in cache
                call_user_func( $this->_callback, $record );
            }

            $status = $this->_readByte();
        }

        return $resultSet;

    }

    /**
     * Read sync command payloads
     *
     * @return array|null
     * @throws PhpOrientBadMethodCallException
     * @throws PhpOrientException
     * @throws SocketException
     */
    public function _read_sync(){

        # type of response
        # decode body char with flag continue ( Header already read )
        $response_type = $this->_readChar();
        $res = [];

        switch( $response_type ){
            case 'n':
                # get end Line \x00
                $this->_readChar();
                $res = array( null );
                break;
            case $response_type == 'r' || $response_type == 'w':
                $res = [ Record::fromConfig( $this->_readRecord() ) ];
                # get end Line \x00
                $this->_readChar();
                break;
            case 'a':
                $res = [ $this->_readString() ];
                # get end Line \x00
                $this->_readChar();
                break;
            case 'l':
                $list_len = $this->_readInt();

                for( $n = 0; $n < $list_len; $n++ ){
                    $res[] = Record::fromConfig( $this->_readRecord() );
                }

                # async-result-type can be:
                # 0: no records remain to be fetched
                # 1: a record is returned as a result set
                # 2: a record is returned as pre-fetched to be loaded in client's
                #       cache only. It's not part of the result set but the client
                #       knows that it's available for later access
                $cached_results = $this->_read_prefetch_record();
                $res = array_merge( $res, $cached_results );
                # cache = cached_results['cached']
                break;
            default:
                # debug errors

                if( !Constants::$LOGGING ){
                    throw new PhpOrientException( 'Unknown payload type ' . $response_type );
                }

                $msg = '';
                $m = $this->_transport->getSocket()->read(1);
                while( $m != '' ){
                    $msg .= $m;
                    $this->_transport->hexDump( $msg );
                    $m = $this->_transport->getSocket()->read(1);
                }

                break;
        }

        return $res;

    }

}
