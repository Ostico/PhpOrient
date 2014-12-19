<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Exceptions\PhpOrientException;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\NeedDBOpenedTrait;

class DbCountRecords extends Operation {
    use NeedDBOpenedTrait;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::DB_COUNT_RECORDS_OP;

    /**
     * @var string The database storage type.
     */
    public $storage_type = Constants::STORAGE_TYPE_PLOCAL;

    /**
     * Write the data to the socket.
     */
    protected function _write() {}

    /**
     * Read the response from the socket.
     *
     * @return int The record count
     */
    protected function _read() {
        return $this->_readLong();
    }

}