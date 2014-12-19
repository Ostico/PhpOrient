<?php

namespace PhpOrient\Protocols\Binary\Operations;


use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Binary\OrientSocket;
use PhpOrient\Protocols\Binary\Serialization\CSV;
use PhpOrient\Protocols\Common\Constants;

class RecordCreate extends Operation {
    /**
     * @var int The op code.
     */
    protected $opCode = Constants::RECORD_CREATE_OP;

    /**
     * @var int The id of the cluster for the record.
     */
    public $cluster_id = 0;

    /**
     * @var int The data segment for the record.
     */
    public $segment = -1;

    /**
     * @var Record The record to add.
     */
    public $record;

    /**
     * @var int The record type
     */
    public $record_type = Constants::RECORD_TYPE_DOCUMENT;

    /**
     * @var int The operation mode. 0 = Synchronous
     */
    public $mode = 0;

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        if( $this->_transport->getProtocolVersion() < 24 ){
            $this->_writeInt( $this->segment );
        }

        $this->_writeShort( $this->cluster_id );
        $this->_writeBytes( CSV::serialize( $this->record ) );

        // record type
        $this->_writeChar( $this->record_type );
        $this->_writeByte( $this->mode );
    }

    /**
     * Read the response from the socket.
     *
     * @return Record The record instance, with RID
     */
    protected function _read() {

        # skip execution in case of transaction
        if( $this->_transport->inTransaction ){
            return $this;
        }

        if( $this->_transport->getProtocolVersion() > 25 ){
            $clusterID = $this->_readShort();
        } else {
            $clusterID = $this->cluster_id;
        }

        $position = $this->_readLong();
        $version  = $this->_readInt();

        # There are some strange behaviours with protocols between 19 and 23
        # the INT ( count-of-collection-changes ) in documentation
        # is present, but don't know why,
        # not every time this INT is present
        # So, i double check for protocol here
        # and add a socket timeout.
        if ( $this->_transport->getProtocolVersion() > 21 ) {

            $changesNum = $this->_readInt();
            $changes = [ ];
            if ( $changesNum > 0 && $this->_transport->getProtocolVersion() > 23 ) {
                for ( $i = 0; $i < $changesNum; $i++ ) {
                    $change = [
                        'uuid-most-sig-bits'  => $this->_readLong(),
                        'uuid-least-sig-bits' => $this->_readLong(),
                        'updated-file-id'     => $this->_readLong(),
                        'updated-page-index'  => $this->_readLong(),
                        'updated-page-offset' => $this->_readInt(),
                    ];
                    $changes[ ] = $change;
                }
            }

        }

        $this->record->setRid( new ID( $clusterID, $position ) );
        $this->record->setVersion( $version );

//        TODO: decide if useful
//        return [ 'record' => $this->record, 'changes' => $changes ];
        return $this->record;

    }


}
