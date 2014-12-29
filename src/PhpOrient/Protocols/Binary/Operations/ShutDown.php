<?php
/**
 * User: gremorian
 * Date: 29/12/14
 * Time: 15.20
 * 
 */

namespace PhpOrient\Protocols\Binary\Operations;


use PhpOrient\Protocols\Binary\Abstracts\NeedConnectedTrait;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Common\ClusterMap;
use PhpOrient\Protocols\Common\Constants;

class ShutDown extends Operation {
    use NeedConnectedTrait;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::SHUTDOWN_OP;

    /**
     * @var string the username to connect with.
     */
    public $username;

    /**
     * @var string the password to connect with.
     */
    public $password;

    /**
     * Write the data to the socket.
     */
    protected function _write() {
        $this->_writeString( $this->username );
        $this->_writeString( $this->password );
    }

    /**
     * Close the socket.
     *
     * @return int
     */
    protected function _read() {
        $clusters = $this->_transport->getClusterMap();
        $clusters = new ClusterMap();
        $this->_socket->__destruct();
        $this->_transport->debug("Database ShutDown");
        return 0;
    }


}