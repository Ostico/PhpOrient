<?php

namespace PhpOrient\Protocols\Binary\Streams;

class DbCreate extends AbstractOperation {
    /**
     * @var int The op code.
     */
    public $opCode = 4;

    /**
     * @var string The name of the database to create.
     */
    public $database;

    /**
     * @var string The database type.
     */
    public $type = 'graph';

    /**
     * @var string The database storage type.
     */
    public $storage = 'plocal';

    /**
     * Write the data to the socket.
     */
    protected function write() {
        $this->writeString( $this->database );
        $this->writeString( $this->type );
        $this->writeString( $this->storage );
    }

    /**
     * Read the response from the socket.
     *
     * @return bool true if the database was created.
     */
    protected function read() {
        return true;
    }

}
