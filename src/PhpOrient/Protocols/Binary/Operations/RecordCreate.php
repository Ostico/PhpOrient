<?php

namespace PhpOrient\Protocols\Binary\Streams;


use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Data\Record;

class RecordCreate extends Operation {
    /**
     * @var int The op code.
     */
    public $opCode = 31;

    /**
     * @var int The id of the cluster for the record.
     */
    public $cluster;

    /**
     * @var int The data segment for the record.
     */
    public $segment = -1;

    /**
     * @var Record The record to add.
     */
    public $record;

    /**
     * @var int The operation mode.
     */
    public $mode = 0;

    /**
     * Write the data to the socket.
     */
    protected function _write() {
        $this->_writeInt( $this->segment );
        $this->_writeShort( $this->cluster );
        $this->_writeBytes( Serializer::serialize( $this->record ) );

        // record type
        if ( $this->record instanceof DocumentInterface ) {
            $this->_writeChar( 'd' );
        } else {
            $this->_writeChar( 'b' ); // @todo determine from record
        }

        $this->_writeByte( $this->mode );
    }

    /**
     * Read the response from the socket.
     *
     * @return RecordInterface The record instance, with RID
     */
    protected function read() {
        $this->record->setId( new ID( $this->cluster, $this->readLong() ) );
        $this->record->setVersion( $this->readInt() );

        return $this->record;
    }


}
