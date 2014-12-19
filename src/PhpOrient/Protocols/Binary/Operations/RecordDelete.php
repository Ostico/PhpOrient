<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Data\ID;

/**
 * RECORD DELETE
 *
 * Delete a record by its RecordID.
 * During the optimistic transaction the record will be deleted only if the versions match.
 * Returns true if has been deleted otherwise false.
 *
 * Request: (cluster-id:short)(cluster-position:long)(record-version:int)(mode:byte)
 * Response: (payload-status:byte)
 * Where:
 *
 * mode is:
 * 0 = synchronous (default mode waits for the answer)
 * 1 = asynchronous (don't need an answer)
 * payload-status returns 1 if the record has been deleted, otherwise 0.
 * If the record didn't exist 0 is returned.
 *
 */
class RecordDelete extends Operation {
    /**
     * @var int The op code.
     */
    protected $opCode = Constants::RECORD_DELETE_OP;

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
     * Only needed for transactions
     * @var string The record type
     */
    public $record_type = Constants::RECORD_TYPE_DOCUMENT;

    /**
     * @var int Record version number
     */
    public $record_version = -1;

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        if ( !empty( $this->rid ) && $this->rid instanceof ID ) {
            $this->cluster_id  = $this->rid->cluster;
            $this->cluster_position = $this->rid->position;
        }

        $this->_writeShort( $this->cluster_id );
        $this->_writeLong( $this->cluster_position );
        $this->_writeInt( $this->record_version );
        $this->_writeBoolean( $this->mode );

    }

    /**
     * Read the response from the socket.
     *
     * @return boolean Returns true if has been deleted otherwise false.
     */
    protected function _read() {
        return $this->_readBoolean();
    }


}
