<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\NeedConnectedTrait;

class DbList extends Operation {
    use NeedConnectedTrait;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::DB_LIST_OP;


    /**
     * Write the data to the socket.
     */
    protected function _write() {}

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function _read() {
        return $this->_readSerialized();
    }

}
