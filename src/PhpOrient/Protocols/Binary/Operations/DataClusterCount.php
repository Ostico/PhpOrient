<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Exceptions\PhpOrientException;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\NeedDBOpenedTrait;

class DataClusterCount extends Operation {
    use NeedDBOpenedTrait;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::DATA_CLUSTER_COUNT_OP;

    /**
     * @var int[] The ids of the clusters to count records for.
     */
    public $ids = [ ];

    /**
     * @var bool whether to include tombstones in the results.
     */
    public $tombstones = false;

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        if( !is_array( $this->ids ) ){
            if( is_numeric( $this->ids ) ){
                $this->ids = array( $this->ids );
            } else {
                throw new PhpOrientException( "Cluster id must be a number or an array of numbers." );
            }
        }

        $this->_writeShort( count( $this->ids ) );
        foreach ( $this->ids as $id ) {
            $this->_writeShort( $id );
        }

        $this->_writeBoolean( $this->tombstones );

    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function _read() {
        return $this->_readLong();
    }

}
