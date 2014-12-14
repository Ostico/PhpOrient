<?php

namespace PhpOrient\Protocols\Binary\Streams;

use PhpOrient\Exceptions\Exception;
use PhpOrient\Queries\Types\QueryTypeInterface;

class Command extends AbstractDbOperation {
    /**
     * @var int The op code.
     */
    protected $opCode = 41;

    /**
     * @var string The query mode.
     */
    public $mode = 's';

    /**
     * @var QueryTypeInterface The query object.
     */
    public $query;


    /**
     * Write the data to the socket.
     */
    protected function write() {
        $this->writeChar( $this->mode );
        $this->writeBytes( $this->query->binarySerialize() );
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
        $results = [ ];
        foreach ( $payloads as $payload ) {
            switch ( $payload[ 'type' ] ) {
                case 'r':
                case 'p':
                    if ( $payload[ 'content' ] ) {
                        $results[ ] = $this->normalizeRecord( $payload[ 'content' ] );
                    } else {
                        $results[ ] = $payload[ 'content' ];
                    }
                    break;
                case 'f':
                    $results[ ] = $payload[ 'content' ];
                    break;
                case 'l':
                    $collection = [ ];
                    foreach ( $payload[ 'content' ] as $item ) {
                        $collection[ ] = $this->normalizeRecord( $item );
                    }
                    $results[ ] = $collection;
                    break;
                default:
                    throw new Exception( 'Unknown payload type: ' . $payload[ 'type' ] );
            }
        }
        if ( count( $results ) === 1 ) {
            return $results[ 0 ];
        } else {
            return $results;
        }
    }

    protected function readPayload() {
        $first = $this->readByte();
        switch ( $first ) {
            case 0:
                // end of results
                return null;
            case 110;
                // null record
                return [
                        'type'    => 'r',
                        'content' => null
                ];
            case 1:
            case 114:
                // a record
                return [
                        'type'    => 'r',
                        'content' => $this->readRecord()
                ];
            case 2:
                // a prefetched record
                return [
                        'type'    => 'p',
                        'content' => $this->readRecord()
                ];
            case 97:
                // a serialized result
                return [
                        'type'    => 'f',
                        'content' => $this->readString()
                ];
            case 108:
                // a collection of records
                return [
                        'type'    => 'l',
                        'content' => $this->readCollection()
                ];
            default:
                throw new Exception( 'Unknown payload type: ' . $first );
        }
    }


}
