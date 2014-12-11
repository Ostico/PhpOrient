<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 30/11/14
 * Time: 6.33
 *
 */

namespace PhpOrient\Protocols\Common;


class Constants {

    const DATABASE_TYPE_DOCUMENT = 'document';
    const DATABASE_TYPE_GRAPH = 'graph';

    const SHUTDOWN_OP = 1;
    const CONNECT_OP = 2;
    const DB_OPEN_OP = 3;
    const DB_CREATE_OP = 4;
    const DB_CLOSE_OP = 5;
    const DB_EXIST_OP = 6;
    const DB_DROP_OP = 7;
    const DB_SIZE_OP = 8;
    const DB_COUNT_RECORDS_OP = 9;
    const DATA_CLUSTER_ADD_OP = 10;
    const DATA_CLUSTER_DROP_OP = 11;
    const DATA_CLUSTER_COUNT_OP = 12;
    const DATA_CLUSTER_DATA_RANGE_OP = 13;
    const RECORD_LOAD_OP = 30;
    const RECORD_CREATE_OP = 31;
    const RECORD_UPDATE_OP = 32;
    const RECORD_DELETE_OP = 33;
    const COMMAND_OP = 41;
    const TX_COMMIT_OP = 60;
    const DB_RELOAD_OP = 73;
    const DB_LIST_OP = 74;

    const DB_FREEZE_OP = 94;
    const DB_RELEASE_OP = 95;

    const STORAGE_TYPE_LOCAL = 'local';
    const STORAGE_TYPE_PLOCAL = 'plocal';
    const STORAGE_TYPE_MEMORY = 'memory';

    const QUERY_SYNC = "com.orientechnologies.orient.core.sql.query.OSQLSynchQuery";
    const QUERY_ASYNC = "com.orientechnologies.orient.core.sql.query.OSQLAsynchQuery";
    const QUERY_CMD = "com.orientechnologies.orient.core.sql.OCommandSQL";
    const QUERY_GREMLIN = "com.orientechnologies.orient.graph.gremlin.OCommandGremlin";
    const QUERY_SCRIPT = "com.orientechnologies.orient.core.command.script.OCommandScript";

    const SERIALIZATION_DOCUMENT2CSV = "ORecordDocument2csv";
    const SERIALIZATION_SERIAL_BIN = "ORecordSerializerBinary";

    const RECORD_TYPE_BYTES = 'b';
    const RECORD_TYPE_DOCUMENT = 'd';
    const RECORD_TYPE_FLAT = 'f';

    const CLUSTER_TYPE_PHYSICAL = 'PHYSICAL';
    const CLUSTER_TYPE_MEMORY = 'MEMORY';

} 