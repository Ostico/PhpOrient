<?php

namespace PhpOrient;

use PhpOrient\Protocols\Common\ConfigurableInterface;
use PhpOrient\Protocols\Common\ConfigurableTrait;
use PhpOrient\Protocols\Common\TransportInterface;
use PhpOrient\Protocols\Binary\SocketTransport;
use PhpOrient\Exceptions\TransportException;
use PhpOrient\Commons\LogTrait;

/**
 * Class Client
 *
 * @package PhpOrient
 */
class Client implements ConfigurableInterface {

    use ConfigurableTrait, LogTrait;

    /**
     * @var string The server host.
     */
    public $hostname;

    /**
     * @var string The port for the server.
     */
    public $port;

    /**
     * @var string The username for the server.
     */
    public $username;

    /**
     * @var string The password for the server.
     */
    public $password;

    /**
     * @var TransportInterface The transport to use for the connection to the server.
     */
    protected $_transport;

    /**
     * Sets the transport
     *
     * @param \PhpOrient\Protocols\Common\TransportInterface $transport
     *
     * @return $this the current object
     */
    public function setTransport( TransportInterface $transport ) {
        $this->_transport = $this->createTransport( $transport );

        return $this;
    }

    /**
     * Gets the transport
     *
     * @return \PhpOrient\Protocols\Common\AbstractTransport
     */
    public function getTransport() {
        if ( $this->_transport === null ) {
            $this->_transport = $this->createTransport();
        }

        return $this->_transport;
    }

    /**
     * Create a transport instance.
     *
     * @param TransportInterface|null $transport
     *
     * @return Protocols\Binary\SocketTransport the transport interface
     * @throws Exceptions\TransportException
     */
    protected function createTransport( $transport = null ) {

        if ( !$transport instanceof TransportInterface ) {

            if ( is_string( $transport ) ) {

                $_transport = new $transport();

                if ( !$_transport instanceof TransportInterface ) {
                    throw New TransportException( $transport . " is not a valid TransportInterface instance" );
                }

            } else {
                //override with default
                $_transport = new SocketTransport();
            }

            $_transport->configure( array(
                    'hostname' => $this->hostname,
                    'port'     => $this->port,
                    'username' => $this->username,
                    'password' => $this->password
            ) );

        } else {
            $_transport = $transport;
        }

        return $_transport;
    }

    /**
     * Execute the given operation.
     *
     * @param string $operation The name of the operation to prepare.
     * @param array  $params    The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute( $operation, array $params = array() ) {
        return $this->getTransport()->execute( $operation, $params );
    }

}
