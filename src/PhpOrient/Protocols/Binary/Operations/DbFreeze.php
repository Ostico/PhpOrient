<?php

namespace PhpOrient\Protocols\Binary\Streams;

class DbFreeze extends AbstractOperation {
    /**
     * @var int The op code.
     */
    public $opCode = 94;

    /**
     * @var string The name of the database to freeze.
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
     * @return true
     */
    protected function read() {
        return true;
    }

}