PhpOrient\Protocols\Binary\Operations\RecordUpdate
===============

RECORD UPDATE

Update a record. Returns the new record's version.
Request: (cluster-id:short)(cluster-position:long)
  (update-content:boolean)(record-content:bytes)(record-version:int)
  (record-type:byte)(mode:byte)
Response: (record-version:int)(count-of-collection-changes)
  [(uuid-most-sig-bits:long)(uuid-least-sig-bits:long)(updated-file-id:long)
  (updated-page-index:long)(updated-page-offset:int)]*

Where record-type is:
'b': raw bytes
'f': flat data
'd': document

and record-version policy is:
'-1': Document update, version increment, no version control.
'-2': Document update, no version control nor increment.
'-3': Used internal in transaction rollback (version decrement).
'>-1': Standard document update (version control).

and mode is:
0 = synchronous (default mode waits for the answer)
1 = asynchronous (don't need an answer)

and update-content is:
true - content of record has been changed and content should
  be updated in storage
false - the record was modified but its own content has
  not been changed. So related collections (e.g. rig-bags) have to
  be updated, but record version and content should not be.

The last part of response is referred to RidBag management.
Take a look at the main page for more details.


* Class name: RecordUpdate
* Namespace: PhpOrient\Protocols\Binary\Operations
* Parent class: [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)





Properties
----------


### $opCode

    protected integer $opCode





* Visibility: **protected**


### $record

    public \PhpOrient\Protocols\Binary\Data\Record $record





* Visibility: **public**


### $cluster_id

    public integer $cluster_id





* Visibility: **public**


### $cluster_position

    public integer $cluster_position





* Visibility: **public**


### $rid

    public \PhpOrient\Protocols\Binary\Data\ID $rid

Instance of record ID, instead of manually set
cluster_id and cluster_position separately



* Visibility: **public**


### $mode

    public integer $mode





* Visibility: **public**


### $record_type

    public string $record_type = \PhpOrient\Protocols\Common\Constants::RECORD_TYPE_DOCUMENT





* Visibility: **public**


### $record_version

    public integer $record_version = -1





* Visibility: **public**


### $record_version_policy

    public integer $record_version_policy = -1





* Visibility: **public**


### $update_content

    public boolean $update_content = true

True:  content of record has been changed
       and content should be updated in storage
False: the record was modified but its own
       content has not been changed.

So related collections (e.g. rid-bags) have to be updated, but
       record version and content should not be.
NOT USED before protocol 23

* Visibility: **public**


### $_socket

    protected \PhpOrient\Protocols\Binary\OrientSocket $_socket





* Visibility: **protected**


### $_writeStack

    protected array $_writeStack = array()

Stack of elements to compile



* Visibility: **protected**


### $_input_buffer

    protected string $_input_buffer





* Visibility: **protected**


### $_output_buffer

    protected string $_output_buffer





* Visibility: **protected**


### $_transport

    protected \PhpOrient\Protocols\Binary\SocketTransport $_transport





* Visibility: **protected**


### $_callback

    public \Closure $_callback





* Visibility: **public**


Methods
-------


### _write

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_write()

Write the data to the socket.



* Visibility: **protected**
* This method is **abstract**.
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _read

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_read()

Read the response from the socket.



* Visibility: **protected**
* This method is **abstract**.
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### __construct

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::__construct(\PhpOrient\Protocols\Binary\SocketTransport $_transport)

Class constructor



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $_transport **[PhpOrient\Protocols\Binary\SocketTransport](PhpOrient-Protocols-Binary-SocketTransport.md)**



### _checkConditions

    null|void PhpOrient\Protocols\Binary\Abstracts\Operation::_checkConditions(\PhpOrient\Protocols\Binary\SocketTransport $transport)





* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $transport **[PhpOrient\Protocols\Binary\SocketTransport](PhpOrient-Protocols-Binary-SocketTransport.md)**



### _writeHeader

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeHeader()

Write the request header.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _readHeader

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_readHeader()

Read the response header.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### prepare

    \PhpOrient\Protocols\Binary\Abstracts\Operation PhpOrient\Protocols\Binary\Abstracts\Operation::prepare()

Build the operation payload



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### send

    \PhpOrient\Protocols\Binary\Abstracts\Operation PhpOrient\Protocols\Binary\Abstracts\Operation::send()

Send message to orient server



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _dump_streams

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_dump_streams()

Log of input/output stream



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### getResponse

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::getResponse()

Get Response from Server



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _writeByte

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeByte(integer $value)

Write a byte to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $value **integer**



### _readByte

    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readByte()

Read a byte from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _writeChar

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeChar(string $value)

Write a character to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $value **string**



### _readChar

    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readChar()

Read a character from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _writeBoolean

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeBoolean(boolean $value)

Write a boolean to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $value **boolean**



### _readBoolean

    boolean PhpOrient\Protocols\Binary\Abstracts\Operation::_readBoolean()

Read a boolean from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _writeShort

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeShort(integer $value)

Write a short to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $value **integer**



### _readShort

    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readShort()

Read a short from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _writeInt

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeInt(integer $value)

Write an integer to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $value **integer**



### _readInt

    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readInt()

Read an integer from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _writeLong

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeLong(integer $value)

Write a long to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $value **integer**



### _readLong

    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readLong()

Read a long from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _writeString

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeString(string $value)

Write a string to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $value **string**



### _readString

    string|null PhpOrient\Protocols\Binary\Abstracts\Operation::_readString()

Read a string from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _writeBytes

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeBytes(string $value)

Write bytes to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $value **string**



### _readBytes

    string|null PhpOrient\Protocols\Binary\Abstracts\Operation::_readBytes()

Read bytes from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _readError

    \PhpOrient\Exceptions\PhpOrientException PhpOrient\Protocols\Binary\Abstracts\Operation::_readError()

Read an error from the remote server and turn it into an exception.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _readSerialized

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_readSerialized()

Read a serialized object from the remote server.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _readRecord

    array PhpOrient\Protocols\Binary\Abstracts\Operation::_readRecord()

The format depends if a RID is passed or an entire
  record with its content.

In case of null record then -2 as short is passed.

In case of RID -3 is passes as short and then the RID:
  (-3:short)(cluster-id:short)(cluster-position:long).

In case of record:
  (0:short)(record-type:byte)(cluster-id:short)
  (cluster-position:long)(record-version:int)(record-content:bytes)

* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _read_prefetch_record

    array<mixed,\PhpOrient\Protocols\Binary\Data\Record> PhpOrient\Protocols\Binary\Abstracts\Operation::_read_prefetch_record()

Read pre-fetched and async Records



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### _read_sync

    array|null PhpOrient\Protocols\Binary\Abstracts\Operation::_read_sync()

Read sync command payloads



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)




### configure

    \PhpOrient\Protocols\Common\ConfigurableInterface PhpOrient\Protocols\Common\ConfigurableInterface::configure(array $options)

Configure the object.



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\ConfigurableInterface](PhpOrient-Protocols-Common-ConfigurableInterface.md)


#### Arguments
* $options **array** - &lt;p&gt;The options for the object.&lt;/p&gt;



### fromConfig

    static PhpOrient\Protocols\Binary\Abstracts\Operation::fromConfig(array $options)

Return a new class instance configured from the given options.



* Visibility: **public**
* This method is **static**.
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)


#### Arguments
* $options **array** - &lt;p&gt;The options for the newly created class instance.&lt;/p&gt;


