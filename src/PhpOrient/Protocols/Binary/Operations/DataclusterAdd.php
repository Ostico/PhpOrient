<?php

namespace PhpOrient\Protocols\Binary\Streams;

class DataclusterAdd extends AbstractDbOperation {
    /**
     * @var int The op code.
     */
    public $opCode = 10;

    /**
     * @var int The id for the cluster.
     */
    public $id = -1;

    /**
     * @var string The name of the new cluster.
     */
    public $name;

    /**
     * @var string The cluster type.
     */
    public $type;

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
    protected function write() {
        $this->writeString( $this->type );
        $this->writeString( $this->name );
        $this->writeString( $this->location );
        $this->writeString( $this->segmentName );
        $this->writeShort( $this->id );
    }

    /**
     * Read the response from the socket.
     *
     * @return int The cluster id.
     */
    protected function read() {
        return $this->readShort();
    }

}
