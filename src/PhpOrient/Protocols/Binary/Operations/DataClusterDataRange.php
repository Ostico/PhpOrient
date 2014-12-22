<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\NeedDBOpenedTrait;

class DataClusterDataRange extends Operation {
    use NeedDBOpenedTrait;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::DATA_CLUSTER_DATA_RANGE_OP;

    /**
     * @var int The ids of the clusters to count records for.
     */
    public $id;

    /**
     * Write the data to the socket.
     */
    protected function _write() {
        $this->_writeShort( $this->id );
    }

    /**
     * Read the response from the socket.
     *
     * @return int[]|string[]
     */
    protected function _read() {
        $result = array();
        $result[] = $this->_readLong();
        $result[] = $this->_readLong();
        return $result;
    }

}
