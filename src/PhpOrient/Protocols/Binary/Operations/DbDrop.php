<?php

namespace PhpOrient\Protocols\Binary\Streams;

class DbDrop extends AbstractOperation {
    /**
     * @var int The op code.
     */
    public $opCode = 7;

    /**
     * @var string The name of the database to dop.
     */
    public $database;

    /**
     * @var string The database storage type.
     */
    public $storage = 'plocal';

    /**
     * Write the data to the socket.
     */
    protected function write() {
        $this->writeString( $this->database );
        $this->writeString( $this->storage );
    }

    /**
     * Read the response from the socket.
     *
     * @return boolean true if the database was dropped.
     */
    protected function read() {
        return true;
    }

}
