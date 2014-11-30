<?php

namespace PhpOrient\Protocols\Binary\Abstracts;

use PhpOrient\Databases\Database;
use PhpOrient\Records\Document;
use PhpOrient\Records\DocumentInterface;
use PhpOrient\Records\ID;
use PhpOrient\Records\Record;
use PhpOrient\Records\RecordInterface;

abstract class DbOperation extends Operation {
    /**
     * @var Database The database this operation is for.
     */
    public $database;


    /**
     * Normalize a record.
     *
     * @param array $payload The record data.
     *
     * @return array|DocumentInterface|RecordInterface
     */
    protected function normalizeRecord( array $payload ) {
        if ( !isset( $payload[ 'type' ] ) ) {
            return $payload;
        }
        if ( $payload[ 'cluster' ] > 0 ) {
            $class = $this->database->getClasses()->byId( $payload[ 'cluster' ] );
            if ( $payload[ 'type' ] === 'd' ) {
                $record = $this->database->createDocumentInstance( $class );
            } else {
                $record = $this->database->createRecordInstance( $class );
            }
            $record->setClass( $class );
        } elseif ( $payload[ 'type' ] === 'd' ) {
            $record = new Document( $this->database );
        } else {
            $record = new Record( $this->database );
        }
        $record->setId( new ID( $payload[ 'cluster' ], $payload[ 'position' ] ) );
        $record->setVersion( isset( $payload[ 'version' ] ) ? $payload[ 'version' ] : 0 );
        $record->setBytes( $payload[ 'bytes' ] );

        return $record;
    }

}
