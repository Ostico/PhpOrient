<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Exceptions\PhpOrientException;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\NeedDBOpenedTrait;

class DbSize extends Operation {
    use NeedDBOpenedTrait;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::DB_SIZE_OP;

    /**
     * Write the data to the socket.
     */
    protected function _write() {}

    /**
     * Read the response from the socket.
     *
     * @return int|string
     */
    protected function _read() {
        return $this->_readLong();
    }

}