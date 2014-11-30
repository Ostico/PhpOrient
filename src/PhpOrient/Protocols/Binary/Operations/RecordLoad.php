<?php

namespace PhpOrient\Protocols\Binary\Streams;

use PhpOrient\Exceptions\Exception;
use PhpOrient\Records\Deserializer;
use PhpOrient\Records\Document;
use PhpOrient\Records\DocumentInterface;
use PhpOrient\Records\ID;
use PhpOrient\Records\Record;

class RecordLoad extends AbstractDbOperation {
    /**
     * @var int The op code.
     */
    public $opCode = 30;

    /**
     * @var int The id of the cluster for the record.
     */
    public $cluster;

    /**
     * @var int The position of the record in the cluster.
     */
    public $position;

    /**
     * @var string The fetch plan for the record.
     */
    public $fetchPlan = '';

    /**
     * @var bool Whether to ignore the cache, defaults to false.
     */
    public $ignoreCache = false;

    /**
     * @var bool Whether to load tombstones, defaults to false.
     */
    public $tombstones = false;

    /**
     * Write the data to the socket.
     */
    protected function write() {
        $this->writeShort( $this->cluster );
        $this->writeLong( $this->position );
        $this->writeString( $this->fetchPlan );
        $this->writeBoolean( $this->ignoreCache );
        $this->writeBoolean( $this->tombstones );
    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function read() {
        $payloads = [ ];
        while ( ( $payload = $this->readPayload() ) !== null ) {
            $payloads[ ] = $payload;
        }

        $first      = null;
        $references = [ ];

        foreach ( $payloads as $i => $payload ) {
            if ( $i ) {
                $references[ ] = $this->normalizeRecord( $payload );
            } else {
                $first = $this->normalizeRecord( $payload );
            }
        }
        if ( $first instanceof DocumentInterface && count( $references ) ) {
            $first->resolveReferences( $references );
        }

        return $first;
    }


    protected function readPayload() {
        $status  = $this->readByte();
        $payload = [ ];
        switch ( $status ) {
            case 0:
                // no more content
                return null;
            case 1:
                // a normal record
                $payload[ 'cluster' ]  = $this->cluster;
                $payload[ 'position' ] = $this->position;
                $payload[ 'bytes' ]    = $this->readString();
                $payload[ 'version' ]  = $this->readInt();
                $payload[ 'type' ]     = $this->readChar();

                return $payload;
            case 2:
                // prefetched record
                $payload[ 'classId' ] = $this->readShort();
                if ( $payload[ 'classId' ] == -2 || $payload[ 'classId' ] == -3 ) {
                    return $payload;
                }
                $payload[ 'type' ]     = $this->readChar();
                $payload[ 'cluster' ]  = $this->readShort();
                $payload[ 'position' ] = $this->readLong();
                $payload[ 'version' ]  = $this->readInt();
                $payload[ 'bytes' ]    = $this->readString();

                return $payload;
            default:
                throw new Exception( 'Unknown payload status: "' . $status . '"' );
        }
    }


}
