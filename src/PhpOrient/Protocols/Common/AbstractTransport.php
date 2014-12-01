<?php

namespace PhpOrient\Protocols\Common;

abstract class AbstractTransport implements TransportInterface {
    use ConfigurableTrait;

    /**
     * @var string The server host.
     */
    public $hostname = 'localhost';

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
     * @var int The session id for the connection.
     */
    protected $sessionId;

    /**
     * @var int The Protocol id for the connection.
     */
    protected $protocolVersion;

    /**
     * Gets the version of negotiated protocol
     *
     * @return int Protocol Version
     */
    public function getProtocol(){
        return $this->protocolVersion;
    }

    /**
     * Gets the session ID for current connection
     *
     * @return int Session
     */
    public function getSessionId(){
        return $this->sessionId;
    }

}
