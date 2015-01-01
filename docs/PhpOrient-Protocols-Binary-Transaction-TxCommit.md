PhpOrient\Protocols\Binary\Transaction\TxCommit
===============

TX_COMMIT_OP

Commits a transaction. This operation flushes all the pending changes to the server side.
<pre>
 <code>
   Request: (tx-id:int)(using-tx-log:byte)(tx-entry)*(0-byte indicating end-of-records)
   tx-entry: (operation-type:byte)(cluster-id:short)(cluster-position:long)(record-type:byte)(entry-content)

   entry-content for CREATE: (record-content:bytes)
   entry-content for UPDATE: (version:record-version)(content-changed:boolean)(record-content:bytes)
   entry-content for DELETE: (version:record-version)

   Response: (created-record-count:int)[(client-specified-cluster-id:short)(client-specified-cluster-position:long)(created-cluster-id:short)(created-cluster-position:long)]*(updated-record-count:int)[(updated-cluster-id:short)(updated-cluster-position:long)(new-record-version:int)]*(count-of-collection-changes:int)[(uuid-most-sig-bits:long)(uuid-least-sig-bits:long)(updated-file-id:long)(updated-page-index:long)(updated-page-offset:int)]*
  </code>
</pre>

Where:

<ul>
  <li>tx-id is the Transaction's Id</li>
  <li>use-tx-log tells if the server must use the Transaction Log to recover the transaction. 1 = true, 0 = false </li>
  <li>operation-type can be:
      <ul>
          <li>1, for UPDATES</li>
          <li>2, for DELETES</li>
          <li>3, for CREATIONS</li>
      </ul>
   </li>
   <li>record-content depends on the operation type:
       <ul>
           <li>For UPDATED (1): (original-record-version:int)(record-content:bytes)</li>
           <li>For DELETED (2): (original-record-version:int)</li>
           <li>For CREATED (3): (record-content:bytes)</li>
       </ul>
   </li>
</ul>

This response contains two parts: a map of 'temporary' client-generated record ids
to 'real' server-provided record ids for each CREATED record, and a map of
UPDATED record ids to update record-versions.

Look at Optimistic Transaction to know how temporary RecordIDs are managed.

The last part or response is referred to RidBag management.
Take a look at the main page for more details.


* Class name: TxCommit
* Namespace: PhpOrient\Protocols\Binary\Transaction
* Parent class: [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)





Properties
----------


#### $opCode
```php
    protected integer $opCode
```
 



* Visibility: **protected**


#### $_txId
```php
    protected integer $_txId = -1
```
 Transaction Id



* Visibility: **protected**


#### $_operation_stack
```php
    protected array $_operation_stack = array()
```
 List of operation to execute



* Visibility: **protected**


#### $_pre_operation_records
```php
    protected array $_pre_operation_records = array()
```
 Records backup before the transaction execution



* Visibility: **protected**


#### $_operation_records
```php
    protected array $_operation_records = array()
```
 Records after the transaction



* Visibility: **protected**


#### $_temp_cluster_position_seq
```php
    protected integer $_temp_cluster_position_seq = -2
```
 When a record is created in transaction it's position is negative



* Visibility: **protected**


#### $_socket
```php
    protected \PhpOrient\Protocols\Binary\OrientSocket $_socket
```
 



* Visibility: **protected**


#### $_writeStack
```php
    protected array $_writeStack = array()
```
 Stack of elements to compile



* Visibility: **protected**


#### $_input_buffer
```php
    protected string $_input_buffer
```
 



* Visibility: **protected**


#### $_output_buffer
```php
    protected string $_output_buffer
```
 



* Visibility: **protected**


#### $_transport
```php
    protected \PhpOrient\Protocols\Binary\SocketTransport $_transport
```
 



* Visibility: **protected**


#### $_callback
```php
    public \Closure $_callback
```
 



* Visibility: **public**


Methods
-------


### _write
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_write()
```
##### Write the data to the socket.



* Visibility: **protected**
* This method is **abstract**.
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _read
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_read()
```
##### Read the response from the socket.



* Visibility: **protected**
* This method is **abstract**.
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _getTransactionId
```php
    integer PhpOrient\Protocols\Binary\Transaction\TxCommit::_getTransactionId()
```
##### 



* Visibility: **protected**




### begin
```php
    \PhpOrient\Protocols\Binary\Transaction\TxCommit PhpOrient\Protocols\Binary\Transaction\TxCommit::begin()
```
##### Starts a transaction by initializing params



* Visibility: **public**




### commit
```php
    mixed PhpOrient\Protocols\Binary\Transaction\TxCommit::commit()
```
##### 



* Visibility: **public**




### rollback
```php
    mixed PhpOrient\Protocols\Binary\Transaction\TxCommit::rollback()
```
##### 



* Visibility: **public**




### attach
```php
    mixed PhpOrient\Protocols\Binary\Transaction\TxCommit::attach(\PhpOrient\Protocols\Binary\Abstracts\Operation $operation)
```
##### 



* Visibility: **public**


##### Arguments
* $operation **[PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)**



### _checkConditions
```php
    null|void PhpOrient\Protocols\Binary\Abstracts\Operation::_checkConditions(\PhpOrient\Protocols\Binary\SocketTransport $transport)
```
##### 



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $transport **[PhpOrient\Protocols\Binary\SocketTransport](PhpOrient-Protocols-Binary-SocketTransport)**



### __construct
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::__construct(\PhpOrient\Protocols\Binary\SocketTransport $_transport)
```
##### Class constructor



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $_transport **[PhpOrient\Protocols\Binary\SocketTransport](PhpOrient-Protocols-Binary-SocketTransport)**



### _writeHeader
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeHeader()
```
##### Write the request header.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _readHeader
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_readHeader()
```
##### Read the response header.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### prepare
```php
    \PhpOrient\Protocols\Binary\Abstracts\Operation PhpOrient\Protocols\Binary\Abstracts\Operation::prepare()
```
##### Build the operation payload



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### send
```php
    \PhpOrient\Protocols\Binary\Abstracts\Operation PhpOrient\Protocols\Binary\Abstracts\Operation::send()
```
##### Send message to orient server



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _dump_streams
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_dump_streams()
```
##### Log of input/output stream



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### getResponse
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::getResponse()
```
##### Get Response from Server



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _writeByte
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeByte(integer $value)
```
##### Write a byte to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $value **integer**



### _readByte
```php
    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readByte()
```
##### Read a byte from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _writeChar
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeChar(string $value)
```
##### Write a character to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $value **string**



### _readChar
```php
    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readChar()
```
##### Read a character from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _writeBoolean
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeBoolean(boolean $value)
```
##### Write a boolean to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $value **boolean**



### _readBoolean
```php
    boolean PhpOrient\Protocols\Binary\Abstracts\Operation::_readBoolean()
```
##### Read a boolean from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _writeShort
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeShort(integer $value)
```
##### Write a short to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $value **integer**



### _readShort
```php
    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readShort()
```
##### Read a short from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _writeInt
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeInt(integer $value)
```
##### Write an integer to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $value **integer**



### _readInt
```php
    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readInt()
```
##### Read an integer from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _writeLong
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeLong(integer $value)
```
##### Write a long to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $value **integer**



### _readLong
```php
    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readLong()
```
##### Read a long from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _writeString
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeString(string $value)
```
##### Write a string to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $value **string**



### _readString
```php
    string|null PhpOrient\Protocols\Binary\Abstracts\Operation::_readString()
```
##### Read a string from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _writeBytes
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeBytes(string $value)
```
##### Write bytes to the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $value **string**



### _readBytes
```php
    string|null PhpOrient\Protocols\Binary\Abstracts\Operation::_readBytes()
```
##### Read bytes from the socket.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _readError
```php
    \PhpOrient\Exceptions\PhpOrientException PhpOrient\Protocols\Binary\Abstracts\Operation::_readError()
```
##### Read an error from the remote server and turn it into an exception.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _readSerialized
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_readSerialized()
```
##### Read a serialized object from the remote server.



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _readRecord
```php
    array PhpOrient\Protocols\Binary\Abstracts\Operation::_readRecord()
```
##### The format depends if a RID is passed or an entire
  record with its content.

In case of null record then -2 as short is passed.

In case of RID -3 is passes as short and then the RID:
  (-3:short)(cluster-id:short)(cluster-position:long).

In case of record:
  (0:short)(record-type:byte)(cluster-id:short)
  (cluster-position:long)(record-version:int)(record-content:bytes)

* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _read_prefetch_record
```php
    array<mixed,\PhpOrient\Protocols\Binary\Data\Record> PhpOrient\Protocols\Binary\Abstracts\Operation::_read_prefetch_record()
```
##### Read pre-fetched and async Records



* Visibility: **protected**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### _read_sync
```php
    array|null PhpOrient\Protocols\Binary\Abstracts\Operation::_read_sync()
```
##### Read sync command payloads



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)




### configure
```php
    \PhpOrient\Protocols\Common\ConfigurableInterface PhpOrient\Protocols\Common\ConfigurableInterface::configure(array $options)
```
##### Configure the object.



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\ConfigurableInterface](PhpOrient-Protocols-Common-ConfigurableInterface)


##### Arguments
* $options **array** <p>The options for the object.</p>



### fromConfig
```php
    static PhpOrient\Protocols\Binary\Abstracts\Operation::fromConfig(array $options)
```
##### Return a new class instance configured from the given options.



* Visibility: **public**
* This method is **static**.
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)


##### Arguments
* $options **array** <p>The options for the newly created class instance.</p>


