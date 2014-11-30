<?php

namespace PhpOrient;

use PhpOrient\Configuration\ConfigurableInterface;
use PhpOrient\Configuration\ConfigurableTrait;
use PhpOrient\Exceptions\SocketException;
use PhpOrient\Exceptions\TransportException;
use PhpOrient\Protocols\Streams\TransportInterface;

/**
 * Class Client
 *
 * @package PhpOrient
 */
class Client implements ConfigurableInterface {

    use ConfigurableTrait;

    /**
     * @var string The server hostname.
     */
    public $hostname = 'localhost';

    /**
     * @var string The port for the server.
     */
    public $port;

    /**
     * @var string The username for the server.
     */
    public $username = 'root';

    /**
     * @var string The password for the server.
     */
    public $password = 'root';

    /**
     * @var DatabaseList The database objects.
     */
    protected $databases;

    /**
     * @var TransportInterface The transport to use for the connection to the server.
     */
    protected $_transport;

    /**
     * Sets the transport
     *
     * @param \PhpOrient\Protocols\Streams\TransportInterface $transport
     *
     * @return $this the current object
     */
    public function setTransport( $transport ) {
        $this->_transport = $this->createTransport( $transport );

        return $this;
    }

    /**
     * Gets the transport
     *
     * @return \PhpOrient\Protocols\Streams\TransportInterface
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
     * @return Protocols\Binary\Transport the transport interface
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
                $_transport = new Protocols\Binary\Transport();
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
     * @param string $operation The name of the operation to execute.
     * @param array  $params    The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute( $operation, array $params = array() ) {
        return $this->getTransport()->execute( $operation, $params );
    }

    /**
     * Gets the Databases
     *
     * @param bool $reload Whether the list of databases should be reloaded from the server.
     *
     * @return \PhpOrient\Databases\DatabaseList
     */
    public function getDatabases( $reload = false ) {
        if ( $this->databases === null ) {
            $this->databases = new DatabaseList( $this );
        }
        if ( $reload ) {
            $this->databases->reload();
        }

        return $this->databases;
    }

    /**
     * Get a database with the given name.
     *
     * @param string $name   The name of the database to get.
     * @param bool   $reload Whether to reload the database list.
     *
     * @return null|Database The database instance, or null if it doesn't exist.
     */
    public function getDatabase( $name, $reload = false ) {
        $databases = $this->getDatabases( $reload );
        if ( isset( $databases[ $name ] ) ) {
            return $databases[ $name ];
        } else {
            return null;
        }
    }


}
