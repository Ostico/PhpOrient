<?php

namespace PhpOrient\Protocols\Binary\Transaction;

use PhpOrient\Exceptions\PhpOrientBadMethodCallException;
use PhpOrient\Protocols\Binary\Abstracts\NeedDBOpenedTrait;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Operations\RecordCreate;
use PhpOrient\Protocols\Binary\Operations\RecordDelete;
use PhpOrient\Protocols\Binary\Operations\RecordUpdate;
use PhpOrient\Protocols\Binary\Serialization\CSV;
use PhpOrient\Protocols\Common\Constants;

/**
 * TX_COMMIT_OP
 *
 * Commits a transaction. This operation flushes all the pending changes to the server side.
 * <pre>
 *  <code>
 *    Request: (tx-id:int)(using-tx-log:byte)(tx-entry)*(0-byte indicating end-of-records)
 *    tx-entry: (operation-type:byte)(cluster-id:short)(cluster-position:long)(record-type:byte)(entry-content)
 *
 *    entry-content for CREATE: (record-content:bytes)
 *    entry-content for UPDATE: (version:record-version)(content-changed:boolean)(record-content:bytes)
 *    entry-content for DELETE: (version:record-version)
 *
 *    Response: (created-record-count:int)[(client-specified-cluster-id:short)(client-specified-cluster-position:long)(created-cluster-id:short)(created-cluster-position:long)]*(updated-record-count:int)[(updated-cluster-id:short)(updated-cluster-position:long)(new-record-version:int)]*(count-of-collection-changes:int)[(uuid-most-sig-bits:long)(uuid-least-sig-bits:long)(updated-file-id:long)(updated-page-index:long)(updated-page-offset:int)]*
 *   </code>
 * </pre>
 *
 * Where:
 *
 * <ul>
 *   <li>tx-id is the Transaction's Id</li>
 *   <li>use-tx-log tells if the server must use the Transaction Log to recover the transaction. 1 = true, 0 = false </li>
 *   <li>operation-type can be:
 *       <ul>
 *           <li>1, for UPDATES</li>
 *           <li>2, for DELETES</li>
 *           <li>3, for CREATIONS</li>
 *       </ul>
 *    </li>
 *    <li>record-content depends on the operation type:
 *        <ul>
 *            <li>For UPDATED (1): (original-record-version:int)(record-content:bytes)</li>
 *            <li>For DELETED (2): (original-record-version:int)</li>
 *            <li>For CREATED (3): (record-content:bytes)</li>
 *        </ul>
 *    </li>
 * </ul>
 *
 * This response contains two parts: a map of 'temporary' client-generated record ids
 * to 'real' server-provided record ids for each CREATED record, and a map of
 * UPDATED record ids to update record-versions.
 *
 * Look at Optimistic Transaction to know how temporary RecordIDs are managed.
 *
 * The last part or response is referred to RidBag management.
 * Take a look at the main page for more details.
 *
 */
class TxCommit extends Operation {
    use NeedDBOpenedTrait;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::TX_COMMIT_OP;

    /**
     * Transaction Id
     *
     * @var int
     */
    protected $_txId = -1;

    /**
     * List of operation to execute
     * @var array
     */
    protected $_operation_stack = [ ];

    /**
     * Records backup before the transaction execution
     *
     * @var array
     */
    protected $_pre_operation_records = [ ];

    /**
     * Records after the transaction
     *
     * @var array
     */
    protected $_operation_records = [ ];

    /**
     * When a record is created in transaction it's position is negative
     * @var int
     */
    protected $_temp_cluster_position_seq = -2;

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        $this->_writeInt( $this->_getTransactionId() );
        $this->_writeBoolean( true );

        foreach( $this->_operation_stack as $k => $operation_fields ){
            $this->_writeByte( 1 );
            foreach( $operation_fields as $field  ){
                $this->{$field[0]}( $field[1] );
            }
        }
        $this->_writeByte( 0 );
        $this->_writeString( '' );

    }

    /**
     * Read the response from the socket.
     *
     * @return mixed the response.
     */
    protected function _read() {

        $result = [
            'created' => [],
            'updated' => [],
            'changes' => []
        ];

        $items = $this->_readInt();
        for( $x = 0; $x < $items; $x++ ){

            # (created-record-count:int)
            # [
            #     (client-specified-cluster-id:short)
            #     (client-specified-cluster-position:long)
            #     (created-cluster-id:short)
            #     (created-cluster-position:long)
            # ]*
            $lastCreated = [
                    'client_c_id'   => $this->_readShort(),
                    'client_c_pos'  => $this->_readLong(),
                    'created_c_id'  => $this->_readShort(),
                    'created_c_pos' => $this->_readLong()
            ];
            $result[ 'created' ][ ] = $lastCreated;

            /**
             * @var RecordCreate $operation
             */
            $operation = $this->_pre_operation_records[
                ( new ID( $lastCreated[ 'client_c_id' ], $lastCreated[ 'client_c_pos' ] ) )->__toString()
            ];

            $rid = new ID( $lastCreated[ 'created_c_id' ], $lastCreated[ 'created_c_pos' ] );
            $operation->record->setVersion( 1 )->setRid( $rid );
            $this->_operation_records[ $rid->__toString() ] = $operation->record;

        }

        $items = $this->_readInt();
        for( $x = 0; $x < $items; $x++ ){
            # (updated-record-count:int)
            # [
            # (updated-cluster-id:short)
            #     (updated-cluster-position:long)
            #     (new-record-version:int)
            # ]*
            $lastUpdated = [
                    'updated_c_id'  => $this->_readShort(),
                    'updated_c_pos' => $this->_readLong(),
                    'new_version'   => $this->_readInt(),
            ];

            # Continue, server send in the updated records
            # even the new the new created ones
            if ( !isset( $this->_pre_operation_records[
                        ( new ID( $lastUpdated[ 'updated_c_id' ], $lastUpdated[ 'updated_c_pos' ] ) )->__toString()
                    ] ) ) {
                continue;
            }

            /**
             * @var RecordUpdate $operation
             */
            $operation = $this->_pre_operation_records[
                ( new ID( $lastUpdated[ 'updated_c_id' ], $lastUpdated[ 'updated_c_pos' ] ) )->__toString()
            ];

            $rid = new ID( $lastUpdated[ 'updated_c_id' ], $lastUpdated[ 'updated_c_pos' ] );
            $operation->record
                    ->setVersion( $lastUpdated[ 'new_version' ] )
                    ->setRid( $rid );

            $this->_operation_records[ $rid->__toString() ] = $operation->record;

        }

        if( $this->_transport->getProtocolVersion() > 23 ){
            $items = $this->_readInt();
            for( $x = 0; $x < $items; $x++ ){
                # (count-of-collection-changes:int)
                # [
                # (uuid-most-sig-bits:long)
                #     (uuid-least-sig-bits:long)
                #     (updated-file-id:long)
                #     (updated-page-index:long)
                #     (updated-page-offset:int)
                # ]*
                $result[ 'updated' ][ ] = [
                        'uuid_high'   => $this->_readLong(),
                        'uuid_low'    => $this->_readLong(),
                        'file_id'     => $this->_readLong(),
                        'page_index'  => $this->_readLong(),
                        'page_offset' => $this->_readLong(),
                ];

            }


        }

        return $this->_operation_records;

    }

    /**
     *
     * @return int
     */
    protected function _getTransactionId(){

        if( $this->_txId < 0 ){

            $myEpoch = strtotime('2014-12-01');
            $myNow = microtime( true ) - $myEpoch;
            $myNowMS = (int)( $myNow * 1000 );
            $Shifted = $myNowMS << 12;
            $this->_txId = $Shifted + mt_rand( 0, 4095 );

            # remove sign
            # treat as unsigned even when the INT is signed
            # and take 4 Bytes
            #   ( 32 bit uniqueness is not ensured in any way,
            #     but is surely unique in this session )
            # we need only a transaction unique for this session
            # not a real UUID
            if ( $this->_txId & 0x80000000 ){
                $this->_txId = ( $this->_txId - 0x80000000 ) & 0xFFFFFFFF;
            } else {
                $this->_txId = $this->_txId & 0xFFFFFFFF;
            }

        }

        return $this->_txId;

    }

    /**
     * Starts a transaction by initializing params
     *
     * @return $this
     */
    public function begin(){
        $this->_operation_stack = [];
        $this->_pre_operation_records = [];
        $this->_operation_records = [];
        $this->_temp_cluster_position_seq = -2;
        $this->_transport->inTransaction = true;
        $this->_getTransactionId();
        return $this;
    }

    public function commit(){
        $this->_transport->inTransaction = false;
        $result = $this->prepare()->send()->getResponse();
        $this->_pre_operation_records = [];
        $this->_operation_stack = [];
        $this->_txId = -1;
        $this->_temp_cluster_position_seq = -2;
        return $result;
    }

    public function rollback() {
        $this->_operation_stack = [];
        $this->_pre_operation_records = [];
        $this->_txId = -1;
        $this->_temp_cluster_position_seq = -2;
        $this->_transport->inTransaction = false;
        return $this;
    }

    public function attach( Operation $operation ){

        if( $operation instanceof RecordUpdate ) {
             $operationPayload = [
                    [ '_writeByte', 1 ],
                    [ '_writeShort', $operation->cluster_id ],
                    [ '_writeLong', (string)$operation->cluster_position ],
                    [ '_writeChar', $operation->record_type ],
                    [ '_writeInt', $operation->record_version ],
                    [ '_writeString', CSV::serialize( $operation->record ) ]
            ];
            if( $this->_transport->getProtocolVersion() >= 23 ){
                $operationPayload[] = [ '_writeBoolean', $operation->update_content ];
            }
            $this->_operation_stack[ ] = $operationPayload;
            $this->_pre_operation_records[ $operation->record->getRid()->__toString() ] = $operation;
        } elseif( $operation instanceof RecordDelete ){
            $this->_operation_stack[ ] = [
                    [ '_writeByte', 2 ],
                    [ '_writeShort', $operation->cluster_id ],
                    [ '_writeLong', (string)$operation->cluster_position ],
                    [ '_writeChar', $operation->record_type ],
                    [ '_writeInt', $operation->record_version ]
            ];
        } elseif( $operation instanceof RecordCreate ){
            $this->_operation_stack[ ] = [
                    [ '_writeByte', 3 ],
                    [ '_writeShort', -1 ],
                    [ '_writeLong', (string)$this->_temp_cluster_position_seq ],
                    [ '_writeChar', $operation->record_type ],
                    [ '_writeString', CSV::serialize( $operation->record ) ]
            ];
            $this->_pre_operation_records[
                ( new ID( -1, $this->_temp_cluster_position_seq ) )->__toString()
            ] = $operation;
            $this->_temp_cluster_position_seq--;
        } else {
            throw new PhpOrientBadMethodCallException(
                    "Wrong command type " . get_class( $operation )
            );
        }

        return $this;

    }

}

