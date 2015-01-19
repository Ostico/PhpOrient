<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Binary\Serialization\CSV;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Abstracts\NeedDBOpenedTrait;
use Closure;

class RecordLoad extends Operation {
    use NeedDBOpenedTrait;

    /**
     * @var Closure|string
     */
    public $_callback;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::RECORD_LOAD_OP;

    /**
     * @var int The id of the cluster for the record.
     */
    public $cluster_id;

    /**
     * @var int The position of the record in the cluster.
     */
    public $cluster_position;

    /**
     * Rid representation
     *
     * @var ID
     */
    public $rid;

    /**
     * @var string The fetch plan for the record.
     */
    public $fetch_plan = '*:0';

    /**
     * @var bool Whether to ignore the cache, defaults to false.
     */
    public $ignore_cache = false;

    /**
     * @var bool Whether to load tombstones, defaults to false.
     */
    public $tombstones = false;

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        if ( !empty( $this->rid ) && $this->rid instanceof ID ) {
            $this->cluster_id  = $this->rid->cluster;
            $this->cluster_position = $this->rid->position;
        }

        $this->_writeShort( $this->cluster_id );
        $this->_writeLong( $this->cluster_position );
        $this->_writeString( $this->fetch_plan );
        $this->_writeBoolean( $this->ignore_cache );
        $this->_writeBoolean( $this->tombstones );

    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function _read() {

        $payloads = [ ];

        $status = $this->_readByte();
        if( $status != 0 ){

            $payload  = [ ];

            // a normal record
            if( $this->_transport->getProtocolVersion() > 27 ){
                $type    = $this->_readChar();
                $version = $this->_readInt();
                if( $type == 'b' ){
                    $data[] = $this->_readString();
                } else {
                    $data    = CSV::unserialize( $this->_readString() );
                }
            } else {
                $string  = $this->_readString();
                $data    = CSV::unserialize( $string );
                $version = $this->_readInt();
                $type    = $this->_readChar();
                if( $type == 'b' ) $data = $string;
            }

            $payload[ 'rid' ]      = new ID( $this->cluster_id, $this->cluster_position );
            $payload[ 'type' ]     = $type;
            $payload[ 'version' ]  = $version;
            if ( isset( $data[ 'oClass' ] ) ) {
                $payload[ 'oClass' ]   = $data[ 'oClass' ];
                unset( $data[ 'oClass' ] );
            }
            $payload[ 'oData' ]    = $data;

            $record = Record::fromConfig( $payload );

            $payloads[ ] = $record;

            $prefetched = $this->_read_prefetch_record();  # read cache and prefetch with callback

            $payloads = array_merge( $payloads, $prefetched );

        }

        return $payloads;

    }

}
