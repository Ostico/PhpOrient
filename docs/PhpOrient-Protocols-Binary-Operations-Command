PhpOrient\Protocols\Binary\Operations\Command
===============

COMMAND_OP

Executes remote commands:

<pre>
<code>
Request:
    - (mode:byte)(class-name:string)(command-payload-length:int)(command-payload)

Response:
    - synchronous commands:  [(sync-result-type:byte)[(sync-result-content:?)]]+
    - asynchronous commands: [(async-result-type:byte)[(async-result-content:?)]*](pre-fetched-record-size.md)[(pre-fetched-record)]*+
</code>
</pre>

Where the request:

<ul>
<li>
<strong>mode</strong> can be 'a' for asynchronous mode and 's' for synchronous mode
</li>
<li>
<strong>class-name</strong> is the class name of the command implementation. There are short form for the most
common commands:

<ul>
<li>
'q' ) stands for query as idempotent command. It's like passing com.orientechnologies.orient.core.sql.query.OSQLSynchQuery
</li>
<li>
'c' ) stands for command as non-idempotent command (insert, update, etc). It's like passing com.orientechnologies.orient.core.sql.OCommandSQL
</li>
<li>
's' ) stands for script. It's like passing com.orientechnologies.orient.core.command.script.OCommandScript . Script commands by using any supported server-side scripting like Javascript command. Since v1.0.
</li>
<li>
'any other values' ) is the class name. The command will be created via reflection using the default constructor and invoking the fromStream() method against it
</li>
</ul>
</li>
<li>
<strong>command-payload</strong> is the command's serialized payload (see Network-Binary-Protocol-Commands)
</li>
</ul>

Response is different for synchronous and asynchronous request:

<ul>
<li>
<strong>synchronous</strong>:
</li>
<li>
<strong>sync-result-type</strong> can be:

<ul>
<li>'n', means null result</li>
<li>'r', means single record returned</li>
<li>'l', collection of records. The format is:
  <ul>
  <li>an integer to indicate the collection size</li>
  <li>all the records one by one</li>
  </ul>
</li>
<li>'a', serialized result, a byte[] is sent</li>
</ul>
</li>
<li>
<strong>sync-result-content</strong>, can only be a record
</li>
<li>
<strong>pre-fetched-record-size</strong>, as the number of pre-fetched records not directly part of the result
set but joined to it by fetching
</li>
<li>
<strong>pre-fetched-record</strong> as the pre-fetched record content
</li>
<li>
<strong>asynchronous</strong>:
</li>
<li>
<strong>async-result-type</strong> can be:

<ul>
<li>0: no records remain to be fetched</li>
<li>1: a record is returned as a resultset</li>
<li>2: a record is returned as pre-fetched to be loaded in client's cache only. It's not part of the result
set but the client knows that it's available for later access
</li>
</ul>
</li>
<li>
<strong>async-result-content</strong>, can only be a record
</li>
</ul>


* Class name: Command
* Namespace: PhpOrient\Protocols\Binary\Operations
* Parent class: [PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation)





Properties
----------


#### $opCode
```php
    protected integer $opCode
```
 



* Visibility: **protected**


#### $_mod_byte
```php
    protected string $_mod_byte = 's'
```
 



* Visibility: **protected**


#### $command
```php
    public string $command = \PhpOrient\Protocols\Common\Constants::QUERY_SYNC
```
 



* Visibility: **public**


#### $query
```php
    public string $query = ''
```
 



* Visibility: **public**


#### $limit
```php
    public integer $limit = 20
```
 



* Visibility: **public**


#### $fetch_plan
```php
    public string $fetch_plan = '*:0'
```
 



* Visibility: **public**


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


