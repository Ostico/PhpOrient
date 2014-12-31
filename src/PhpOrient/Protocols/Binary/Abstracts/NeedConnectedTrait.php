<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 07/12/14
 * Time: 4.27
 * 
 */

namespace PhpOrient\Protocols\Binary\Abstracts;

use PhpOrient\Exceptions\PhpOrientException;
use PhpOrient\Protocols\Binary\SocketTransport;

trait NeedConnectedTrait {

    /**
     * @param $transport
     *
     * @throws PhpOrientException
     */
    protected function _checkConditions( SocketTransport $transport ){
        if( !$transport->connected && !$transport->isRequestToken() ){
            throw new PhpOrientException('Can not perform ' . join( '', array_slice( explode( '\\', get_class( $this ) ), -1 ) ) . ' operation without a connection.');
        }
    }

} 