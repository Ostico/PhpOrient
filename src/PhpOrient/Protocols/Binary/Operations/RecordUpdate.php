<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Binary\OrientSocket;
use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Serialization\CSV;


/**
 * RECORD UPDATE
 *
 * Update a record. Returns the new record's version.
 * Request: (cluster-id:short)(cluster-position:long)
 *   (update-content:boolean)(record-content:bytes)(record-version:int)
 *   (record-type:byte)(mode:byte)
 * Response: (record-version:int)(count-of-collection-changes)
 *   [(uuid-most-sig-bits:long)(uuid-least-sig-bits:long)(updated-file-id:long)
 *   (updated-page-index:long)(updated-page-offset:int)]*
 *
 * Where record-type is:
 * 'b': raw bytes
 * 'f': flat data
 * 'd': document
 *
 * and record-version policy is:
 * '-1': Document update, version increment, no version control.
 * '-2': Document update, no version control nor increment.
 * '-3': Used internal in transaction rollback (version decrement).
 * '>-1': Standard document update (version control).
 *
 * and mode is:
 * 0 = synchronous (default mode waits for the answer)
 * 1 = asynchronous (don't need an answer)
 *
 * and update-content is:
 * true - content of record has been changed and content should
 *   be updated in storage
 * false - the record was modified but its own content has
 *   not been changed. So related collections (e.g. rig-bags) have to
 *   be updated, but record version and content should not be.
 *
 * The last part of response is referred to RidBag management.
 * Take a look at the main page for more details.
 */
class RecordUpdate extends Operation {
    /**
     * @var int The op code.
     */
    protected $opCode = Constants::RECORD_UPDATE_OP;

    /**
     * @var Record The record to add.
     */
    public $record;

    /**
     * @var int The id of the cluster for the record.
     */
    public $cluster_id = 0;

    /**
     * @var int The position of the record in the cluster.
     */
    public $cluster_position = 0;

    /**
     * Instance of record ID, instead of manually set
     * cluster_id and cluster_position separately
     *
     * @var ID
     */
    public $rid;

    /**
     * @var int The operation mode.
     */
    public $mode = 0; //Synchronous mode

    /**
     * @var string The record type
     */
    public $record_type = Constants::RECORD_TYPE_DOCUMENT;

    /**
     * @var int used for transactions
     */
    public $record_version = -1;

    /**
     * @var int Document update, version increment, no version control.
     */
    public $record_version_policy = -1;

    /** True:  content of record has been changed
     *        and content should be updated in storage
     * False: the record was modified but its own
     *        content has not been changed.
     *        So related collections (e.g. rid-bags) have to be updated, but
     *        record version and content should not be.
     * NOT USED before protocol 23
     * @var boolean
     */
    public $update_content = true;

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        if ( !empty( $this->rid ) && $this->rid instanceof ID ) {
            $this->cluster_id  = $this->rid->cluster;
            $this->cluster_position = $this->rid->position;
        }

        $this->record->setRid( new ID( $this->cluster_id, $this->cluster_position ) );

        $this->_writeShort( $this->cluster_id );
        $this->_writeLong( $this->cluster_position );

        if( $this->_transport->getProtocolVersion() >= 23 ){
            $this->_writeBoolean( $this->update_content );
        }

        $this->_writeBytes( CSV::serialize( $this->record ) );
        $this->_writeInt( $this->record_version_policy );
        $this->_writeChar( $this->record_type );
        $this->_writeBoolean( $this->mode );

    }

    /**
     * Read the response from the socket.
     *
     * @return Record The record instance, with RID
     */
    protected function _read() {

        $this->record->setVersion( $this->_readInt() );

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

        return $this->record;
    }


}
