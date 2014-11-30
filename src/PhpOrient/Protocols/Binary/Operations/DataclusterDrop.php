<?php

namespace PhpOrient\Protocols\Binary\Streams;

class DataclusterDrop extends AbstractDbOperation {
    /**
     * @var int The op code.
     */
    public $opCode = 11;

    /**
     * @var int The id for the cluster.
     */
    public $id;

    /**
     * Write the data to the socket.
     */
    protected function write() {
        $this->writeShort( $this->id );
    }

    /**
     * Read the response from the socket.
     *
     * @return boolean True if the datacluster was immediately deleted.
     */
    protected function read() {
        return $this->readBoolean();
    }

}
