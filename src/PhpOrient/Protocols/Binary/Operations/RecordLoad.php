<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Data\RecordPayloadTrait;
use PhpOrient\Protocols\Binary\Serialization\CSV;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Common\NeedDBOpenedTrait;
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
    public $opCode = Constants::RECORD_LOAD_OP;

    /**
     * @var int The id of the cluster for the record.
     */
    public $cluster;

    /**
     * @var int The position of the record in the cluster.
     */
    public $position;

    /**
     * Rid representation
     *
     * @var ID
     */
    public $rid;

    /**
     * @var string The fetch plan for the record.
     */
    public $fetchPlan = '*:0';

    /**
     * @var bool Whether to ignore the cache, defaults to false.
     */
    public $ignoreCache = false;

    /**
     * @var bool Whether to load tombstones, defaults to false.
     */
    public $tombstones = false;

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        if ( !empty( $this->rid ) ) {
            $this->cluster  = $this->rid->cluster;
            $this->position = $this->rid->position;
        }

        $this->_writeShort( $this->cluster );
        $this->_writeLong( $this->position );
        $this->_writeString( $this->fetchPlan );
        $this->_writeBoolean( $this->ignoreCache );
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
            $payload[ 'cluster' ]  = $this->cluster;
            $payload[ 'position' ] = $this->position;
            $payload[ 'oData' ]    = CSV::unserialize( $this->_readString() );
            $payload[ 'version' ]  = $this->_readInt();
            $payload[ 'type' ]     = $this->_readChar();

            $payloads[ ] = $payload;

            $prefetched = $this->_read_prefetch_record();  # read cache and prefetch with callback

            $payloads = array_merge( $payloads, $prefetched );

        }

        return $payloads;

    }

}
