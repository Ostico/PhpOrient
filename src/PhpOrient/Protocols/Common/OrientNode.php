<?php
/**
 * User: gremorian
 * Date: 27/09/15
 * Time: 18.57
 *
 */

namespace PhpOrient\Protocols\Common;


use PhpOrient\Protocols\Binary\Data\Record;

class OrientNode {

    /*
        Represent a server node in a multi clustered configuration
        TODO: extends this object with different listeners if we're going to support in the driver
            an abstraction of the HTTP protocol, for now we are not interested in that
        :param $node_list: dict with starting configs (usually from a db_open, db_reload record response)
    */

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $id;

    /**
     * @var \DateTime
     */
    public $startedOn;

    /**
     * @var string
     */
    public $host;

    /**
     * @var int
     */
    public $port;

    /**
     *
     * @param Record|null $node_list an Array with starting configs (usually from a db_open, db_reload record response or server pushes)
     *
     * @return OrientNode
     */
    public function __construct( Record $node_list = null ){

        $this->id = $node_list['id'];
        $this->name = $node_list['name'];
        $this->startedOn = $node_list['startedOn'];
        $listener = array_reduce( $node_list['listeners'], function( $acc, $lsn ){
            if( $lsn['protocol'] != 'ONetworkProtocolBinary' ) return $acc;
            return $lsn;
        } );

        if ( $listener ){
            $listen = explode( ":", $listener['listen'] );
            $this->host = $listen[0];
            $this->port = $listen[1] ;
        }

        return $this;

    }

    public function __toString(){
        return $this->name;
    }

}