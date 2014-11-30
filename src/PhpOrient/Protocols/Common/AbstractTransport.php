<?php

namespace PhpOrient\Protocols\Common;

use PhpOrient\Configuration\ConfigurableTrait;
use PhpOrient\Configuration\TransportInterface;

abstract class AbstractTransport implements TransportInterface {
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
}
