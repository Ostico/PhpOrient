<?php

namespace PhpOrient\Protocols\Binary;

use PhpOrient\Exceptions\TransportException;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Operations\Connect;
use PhpOrient\Protocols\Binary\Operations\DbOpen;
use PhpOrient\Protocols\Common\AbstractTransport;
use PhpOrient\Protocols\Common\Constants;

class SocketTransport extends AbstractTransport {

    /**
     * @var OrientSocket the connected socket.
     */
    protected $_socket;


    /**
     * Actual database handled
     *
     * @var string
     */
    public $databaseOpened;

    /**
     * Type of serialization
     *
     * @var string Serialization
     */
    public $serializationType = Constants::SERIALIZATION_DOCUMENT2CSV;

    /**
     * Gets the Socket, and establishes the connection if required.
     *
     * @return \PhpOrient\Protocols\Binary\OrientSocket
     */
    protected function _getSocket() {
        if ( $this->_socket === null ) {
            $this->_socket = new OrientSocket( $this->hostname, $this->port );
        }
        return $this->_socket;
    }

    /**
     * Execute the operation with the given name.
     *
     * @param string $operation The operation to prepare.
     * @param array  $params    The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute( $operation, array $params = array() ) {

        $op = $this->operationFactory( $operation, $params );
        $result = $op->prepare()->send()->getResponse();
        $this->sessionId = $op->sessionId;
        return $result;

    }

    /**
     * @param Operation|string $operation The operation name or instance.
     * @param array            $params    The parameters for the operation.
     *
     * @return Operation The operation instance.
     * @throws TransportException
     */
    protected function operationFactory( $operation, array $params ) {

        if ( !( $operation instanceof Operation ) ) {

            if ( !strstr( $operation, '\\' ) ) {
                $operation = 'PhpOrient\Protocols\Binary\Operations\\' . ucfirst( $operation );
            }

            $operation = new $operation( $this->_getSocket() );

            /**
             * Used when we want initialize the transport
             * from client configuration params
             *
             */
            if( $operation instanceof DbOpen || $operation instanceof Connect ){

                if( empty($this->username) && empty($this->password) ){
                    throw new TransportException('Can not initialize a transport ' .
                    'without connection parameters');
                }

                $params[ 'username' ] = $this->username;
                $params[ 'password' ] = $this->password;

            }

        }

        $operation->configure( $params );

        return $operation;
    }

}
