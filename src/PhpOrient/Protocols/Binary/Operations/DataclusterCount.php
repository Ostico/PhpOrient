<?php

namespace PhpOrient\Protocols\Binary\Streams;

class DataclusterCount extends AbstractDbOperation {
    /**
     * @var int The op code.
     */
    public $opCode = 12;

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
    protected function write() {
        $this->writeShort( count( $this->ids ) );
        foreach ( $this->ids as $id ) {
            $this->writeShort( $id );
        }
        $this->writeBoolean( $this->tombstones );
    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function read() {
        return $this->readLong();
    }

}
