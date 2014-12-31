<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\NeedConnectedTrait;

class DbDrop extends Operation {
    use NeedConnectedTrait;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::DB_DROP_OP;

    /**
     * @var string The name of the database to drop.
     */
    public $database;

    /**
     * @var string The database storage_type database_type.
     */
    public $storage_type = Constants::STORAGE_TYPE_PLOCAL;

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        $this->_writeString( $this->database );
        $this->_writeString( $this->storage_type );

    }

    /**
     * Read the response from the socket.
     *
     * @return true if the database was dropped.
     */
    protected function _read() {
        return true;
    }

}
