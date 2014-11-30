<?php

namespace PhpOrient\Protocols\Binary;

use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Operations\Connect;
use PhpOrient\Protocols\Binary\Operations\DbOpen;
use PhpOrient\Protocols\Common\AbstractTransport;

class Transport extends AbstractTransport {
    /**
     * @var Socket the connected socket.
     */
    protected $socket;

    /**
     * @var int The session id for the connection.
     */
    protected $sessionId;

    /**
     * Gets the Socket, and establishes the connection if required.
     *
     * @return \PhpOrient\Protocols\Binary\Socket
     */
    public function getSocket() {
        if ( $this->socket === null ) {
            $this->socket = new Socket( $this->hostname, $this->port );
        }

        return $this->socket;
    }


    /**
     * Execute the operation with the given name.
     *
     * @param string $operation The operation to execute.
     * @param array  $params    The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute( $operation, array $params = array() ) {

        $op = $this->createOperation( $operation, $params );
        $result = $op->execute();
        $this->sessionId = $op->sessionId;
        return $result;

    }

    /**
     * @param Operation|string $operation The operation name or instance.
     * @param array                    $params    The parameters for the operation.
     *
     * @return Operation The operation instance.
     */
    protected function createOperation( $operation, array $params ) {

        if ( !( $operation instanceof Operation ) ) {
            if ( !strstr( $operation, '\\' ) ) {
                $operation = 'PhpOrient\Protocols\Binary\Operations\\' . ucfirst( $operation );
            }
            $operation = new $operation();

            if( $operation instanceof DbOpen || $operation instanceof Connect ){
                $operation->username = $this->username;
                $operation->password = $this->password;
            }

        }

        $operation->socket = $this->getSocket();
        $operation->configure( $params );

        return $operation;
    }

}
