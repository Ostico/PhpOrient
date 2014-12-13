<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\NeedDBOpenedTrait;

class DataClusterAdd extends Operation {
    use NeedDBOpenedTrait;

    /**
     * @var int The op code.
     */
    public $opCode = Constants::DATA_CLUSTER_ADD_OP;

    /**
     * @var int The id for the cluster.
     */
    public $id = -1;

    /**
     * @var string The name of the new cluster.
     */
    public $cluster_name;

    /**
     * @var string The cluster type.
     */
    public $cluster_type = Constants::CLUSTER_TYPE_PHYSICAL;

    /**
     * @var string The cluster location.
     */
    public $location;

    /**
     * @var string The name of the segment.
     */
    public $segmentName;

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        if( $this->_transport->getProtocolVersion() < 24 ){
            $this->_writeString( $this->cluster_type );
            $this->_writeString( $this->cluster_name );
            $this->_writeString( $this->location );
            $this->_writeString( $this->segmentName );
        } else {
            $this->_writeString( $this->cluster_name );
        }

        if($this->_transport->getProtocolVersion() >= 18 ){
            $this->_writeShort( $this->id );
        }

    }

    /**
     * Read the response from the socket.
     *
     * @return int The cluster id.
     */
    protected function _read() {
        return $this->_readShort();
    }

}
