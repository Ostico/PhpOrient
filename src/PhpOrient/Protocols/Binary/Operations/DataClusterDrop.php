<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\NeedDBOpenedTrait;

class DataClusterDrop extends Operation {
    use NeedDBOpenedTrait;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::DATA_CLUSTER_DROP_OP;

    /**
     * @var int The id for the cluster.
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
     * @return boolean True if the DataCluster was immediately deleted.
     */
    protected function _read() {
        $res = $this->_readBoolean();
        if( $res ){
            $clusters = $this->_transport->getClusterMap();
            $clusters->dropClusterID( $res );
        }
        return $res;
    }

}
