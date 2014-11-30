<?php

namespace PhpOrient\Protocols\Binary\Streams;

class DbList extends AbstractOperation {
    /**
     * @var int The op code.
     */
    public $opCode = 74;


    /**
     * Write the data to the socket.
     */
    protected function write() {

    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function read() {
        return $this->readSerialized();
    }

}
