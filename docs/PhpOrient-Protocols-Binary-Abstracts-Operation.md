PhpOrient\Protocols\Binary\Abstracts\Operation
===============






* Class name: Operation
* Namespace: PhpOrient\Protocols\Binary\Abstracts
* This is an **abstract** class
* This class implements: [PhpOrient\Protocols\Common\ConfigurableInterface](PhpOrient-Protocols-Common-ConfigurableInterface.md)




Properties
----------


### $opCode

    protected integer $opCode





* Visibility: **protected**


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


### __construct

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::__construct(\PhpOrient\Protocols\Binary\SocketTransport $_transport)

Class constructor



* Visibility: **public**


#### Arguments
* $_transport **[PhpOrient\Protocols\Binary\SocketTransport](PhpOrient-Protocols-Binary-SocketTransport.md)**



### _write

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_write()

Write the data to the socket.



* Visibility: **protected**
* This method is **abstract**.




### _read

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_read()

Read the response from the socket.



* Visibility: **protected**
* This method is **abstract**.




### _checkConditions

    null|void PhpOrient\Protocols\Binary\Abstracts\Operation::_checkConditions(\PhpOrient\Protocols\Binary\SocketTransport $transport)





* Visibility: **protected**


#### Arguments
* $transport **[PhpOrient\Protocols\Binary\SocketTransport](PhpOrient-Protocols-Binary-SocketTransport.md)**



### _writeHeader

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeHeader()

Write the request header.



* Visibility: **protected**




### _readHeader

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_readHeader()

Read the response header.



* Visibility: **protected**




### prepare

    \PhpOrient\Protocols\Binary\Abstracts\Operation PhpOrient\Protocols\Binary\Abstracts\Operation::prepare()

Build the operation payload



* Visibility: **public**




### send

    \PhpOrient\Protocols\Binary\Abstracts\Operation PhpOrient\Protocols\Binary\Abstracts\Operation::send()

Send message to orient server



* Visibility: **public**




### _dump_streams

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_dump_streams()

Log of input/output stream



* Visibility: **protected**




### getResponse

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::getResponse()

Get Response from Server



* Visibility: **public**




### _writeByte

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeByte(integer $value)

Write a byte to the socket.



* Visibility: **protected**


#### Arguments
* $value **integer**



### _readByte

    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readByte()

Read a byte from the socket.



* Visibility: **protected**




### _writeChar

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeChar(string $value)

Write a character to the socket.



* Visibility: **protected**


#### Arguments
* $value **string**



### _readChar

    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readChar()

Read a character from the socket.



* Visibility: **protected**




### _writeBoolean

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeBoolean(boolean $value)

Write a boolean to the socket.



* Visibility: **protected**


#### Arguments
* $value **boolean**



### _readBoolean

    boolean PhpOrient\Protocols\Binary\Abstracts\Operation::_readBoolean()

Read a boolean from the socket.



* Visibility: **protected**




### _writeShort

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeShort(integer $value)

Write a short to the socket.



* Visibility: **protected**


#### Arguments
* $value **integer**



### _readShort

    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readShort()

Read a short from the socket.



* Visibility: **protected**




### _writeInt

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeInt(integer $value)

Write an integer to the socket.



* Visibility: **protected**


#### Arguments
* $value **integer**



### _readInt

    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readInt()

Read an integer from the socket.



* Visibility: **protected**




### _writeLong

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeLong(integer $value)

Write a long to the socket.



* Visibility: **protected**


#### Arguments
* $value **integer**



### _readLong

    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readLong()

Read a long from the socket.



* Visibility: **protected**




### _writeString

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeString(string $value)

Write a string to the socket.



* Visibility: **protected**


#### Arguments
* $value **string**



### _readString

    string|null PhpOrient\Protocols\Binary\Abstracts\Operation::_readString()

Read a string from the socket.



* Visibility: **protected**




### _writeBytes

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeBytes(string $value)

Write bytes to the socket.



* Visibility: **protected**


#### Arguments
* $value **string**



### _readBytes

    string|null PhpOrient\Protocols\Binary\Abstracts\Operation::_readBytes()

Read bytes from the socket.



* Visibility: **protected**




### _readError

    \PhpOrient\Exceptions\PhpOrientException PhpOrient\Protocols\Binary\Abstracts\Operation::_readError()

Read an error from the remote server and turn it into an exception.



* Visibility: **protected**




### _readSerialized

    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_readSerialized()

Read a serialized object from the remote server.



* Visibility: **protected**




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




### _read_prefetch_record

    array<mixed,\PhpOrient\Protocols\Binary\Data\Record> PhpOrient\Protocols\Binary\Abstracts\Operation::_read_prefetch_record()

Read pre-fetched and async Records



* Visibility: **protected**




### _read_sync

    array|null PhpOrient\Protocols\Binary\Abstracts\Operation::_read_sync()

Read sync command payloads



* Visibility: **public**




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


#### Arguments
* $options **array** - &lt;p&gt;The options for the newly created class instance.&lt;/p&gt;


