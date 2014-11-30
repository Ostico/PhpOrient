<?php

namespace PhpOrient\Protocols\Binary\Streams;

use PhpOrient\Exceptions\Exception;
use PhpOrient\Records\Deserializer;
use PhpOrient\Records\Document;
use PhpOrient\Records\DocumentInterface;
use PhpOrient\Records\ID;
use PhpOrient\Records\Record;
use PhpOrient\Records\RecordInterface;
use PhpOrient\Records\Serializer;

class RecordUpdate extends AbstractDbOperation {
    /**
     * @var int The op code.
     */
    public $opCode = 32;

    /**
     * @var RecordInterface The record to add.
     */
    public $record;

    /**
     * @var int The operation mode.
     */
    public $mode = 0;

    /**
     * Write the data to the socket.
     */
    protected function write() {
        $id = $this->record->getId();
        $this->writeShort( $id->cluster );
        $this->writeLong( $id->position );
        $this->writeBytes( Serializer::serialize( $this->record ) );
        $this->writeInt( $this->record->getVersion() );
        // record type
        if ( $this->record instanceof DocumentInterface ) {
            $this->writeChar( 'd' );
        } else {
            $this->writeChar( 'b' ); // @todo determine from record
        }

        $this->writeByte( $this->mode );
    }

    /**
     * Read the response from the socket.
     *
     * @return RecordInterface The record instance, with RID
     */
    protected function read() {
        $this->record->setVersion( $this->readInt() );

        return $this->record;
    }


}
