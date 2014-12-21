<?php

namespace PhpOrient\Protocols\Binary\Operations;

use PhpOrient\Protocols\Binary\Abstracts\NeedDBOpenedTrait;
use PhpOrient\Protocols\Binary\Abstracts\Operation;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Binary\Stream\Writer;
use PhpOrient\Protocols\Common\Constants;

/**
 * COMMAND_OP
 *
 * Executes remote commands:
 *
 * <pre>
 * <code>
 * Request:
 *     - (mode:byte)(class-name:string)(command-payload-length:int)(command-payload)
 *
 * Response:
 *     - synchronous commands:  [(sync-result-type:byte)[(sync-result-content:?)]]+
 *     - asynchronous commands: [(async-result-type:byte)[(async-result-content:?)]*](pre-fetched-record-size.md)[(pre-fetched-record)]*+
 * </code>
 * </pre>
 *
 * Where the request:
 *
 * <ul>
 * <li>
 * <strong>mode</strong> can be 'a' for asynchronous mode and 's' for synchronous mode
 * </li>
 * <li>
 * <strong>class-name</strong> is the class name of the command implementation. There are short form for the most
 * common commands:
 *
 * <ul>
 * <li>
 * 'q' ) stands for query as idempotent command. It's like passing com.orientechnologies.orient.core.sql.query.OSQLSynchQuery
 * </li>
 * <li>
 * 'c' ) stands for command as non-idempotent command (insert, update, etc). It's like passing com.orientechnologies.orient.core.sql.OCommandSQL
 * </li>
 * <li>
 * 's' ) stands for script. It's like passing com.orientechnologies.orient.core.command.script.OCommandScript . Script commands by using any supported server-side scripting like Javascript command. Since v1.0.
 * </li>
 * <li>
 * 'any other values' ) is the class name. The command will be created via reflection using the default constructor and invoking the fromStream() method against it
 * </li>
 * </ul>
 * </li>
 * <li>
 * <strong>command-payload</strong> is the command's serialized payload (see Network-Binary-Protocol-Commands)
 * </li>
 * </ul>
 *
 * Response is different for synchronous and asynchronous request:
 *
 * <ul>
 * <li>
 * <strong>synchronous</strong>:
 * </li>
 * <li>
 * <strong>sync-result-type</strong> can be:
 *
 * <ul>
 * <li>'n', means null result</li>
 * <li>'r', means single record returned</li>
 * <li>'l', collection of records. The format is:
 *   <ul>
 *   <li>an integer to indicate the collection size</li>
 *   <li>all the records one by one</li>
 *   </ul>
 * </li>
 * <li>'a', serialized result, a byte[] is sent</li>
 * </ul>
 * </li>
 * <li>
 * <strong>sync-result-content</strong>, can only be a record
 * </li>
 * <li>
 * <strong>pre-fetched-record-size</strong>, as the number of pre-fetched records not directly part of the result
 * set but joined to it by fetching
 * </li>
 * <li>
 * <strong>pre-fetched-record</strong> as the pre-fetched record content
 * </li>
 * <li>
 * <strong>asynchronous</strong>:
 * </li>
 * <li>
 * <strong>async-result-type</strong> can be:
 *
 * <ul>
 * <li>0: no records remain to be fetched</li>
 * <li>1: a record is returned as a resultset</li>
 * <li>2: a record is returned as pre-fetched to be loaded in client's cache only. It's not part of the result
 * set but the client knows that it's available for later access
 * </li>
 * </ul>
 * </li>
 * <li>
 * <strong>async-result-content</strong>, can only be a record
 * </li>
 * </ul>
 */
class Command extends Operation {
    use NeedDBOpenedTrait;

    /**
     * @var int The op code.
     */
    protected $opCode = Constants::COMMAND_OP;

    /**
     * @var string
     */
    protected $_mod_byte = 's';

    /**
     * @var string The query mode.
     */
    public $command = Constants::QUERY_SYNC;

    /**
     * @var string The query object.
     */
    public $query = '';

    /**
     * @var int
     */
    public $limit = 20;

    /**
     * @var string The fetch plan for the record.
     */
    public $fetch_plan = '*:0';

    /**
     * Write the data to the socket.
     */
    protected function _write() {

        if( array_search( $this->command, [
            Constants::QUERY_CMD,
            Constants::QUERY_SYNC,
            Constants::QUERY_GREMLIN,
            Constants::QUERY_SCRIPT
        ] ) !== false ){
            $this->_mod_byte = 's';  # synchronous
        } else {
            $this->_mod_byte = 'a';  # asynchronous
        }

        $this->_writeChar( $this->_mod_byte );

        $_payload   = [];
        $_payload[] = Writer::packString( $this->command );

        if( $this->command == Constants::QUERY_SCRIPT ){
            $_payload[] = Writer::packString( 'sql' );
        }

        $_payload[] = Writer::packString( $this->query );

        if( array_search( $this->command, [
                Constants::QUERY_ASYNC,
                Constants::QUERY_SYNC,
                Constants::QUERY_GREMLIN,
            ] ) !== false ){
            $_payload[] = Writer::packInt( $this->limit );
            $_payload[] = Writer::packString( $this->fetch_plan );
        }

        $_payload[] = Writer::packInt( 0 );
        $this->_writeString( implode( "", $_payload ) );

    }

    /**
     * Read the response from the socket.
     *
     * @return Record|Record[]|string
     */
    protected function _read() {

        if( $this->command == Constants::QUERY_ASYNC ){
            return $this->_read_prefetch_record();
        } else {
            $res = $this->_read_sync();
            if( $this->command == Constants::QUERY_CMD ){
                return $res[0];
            }
            return $res;
        }

    }

}
