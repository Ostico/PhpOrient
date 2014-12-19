<?php

namespace PhpOrient\Protocols\Common;

interface TransportInterface extends ConfigurableInterface {

    /**
     * Execute the operation with the given name.
     *
     * @param string $operation The operation to prepare.
     * @param array  $params    The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute( $operation, array $params = array() );

}
