PhpOrient\Protocols\Binary\Abstracts\Operation
===============






* Class name: Operation
* Namespace: PhpOrient\Protocols\Binary\Abstracts
* This is an **abstract** class
* This class implements: [PhpOrient\Protocols\Common\ConfigurableInterface](PhpOrient-Protocols-Common-ConfigurableInterface)




Properties
----------


#### $opCode
```php
    protected integer $opCode
```
 



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


### __construct
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::__construct(\PhpOrient\Protocols\Binary\SocketTransport $_transport)
```
##### Class constructor



* Visibility: **public**


##### Arguments
* $_transport **[PhpOrient\Protocols\Binary\SocketTransport](PhpOrient-Protocols-Binary-SocketTransport)**



### _write
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_write()
```
##### Write the data to the socket.



* Visibility: **protected**
* This method is **abstract**.




### _read
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_read()
```
##### Read the response from the socket.



* Visibility: **protected**
* This method is **abstract**.




### _checkConditions
```php
    null|void PhpOrient\Protocols\Binary\Abstracts\Operation::_checkConditions(\PhpOrient\Protocols\Binary\SocketTransport $transport)
```
##### 



* Visibility: **protected**


##### Arguments
* $transport **[PhpOrient\Protocols\Binary\SocketTransport](PhpOrient-Protocols-Binary-SocketTransport)**



### _writeHeader
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeHeader()
```
##### Write the request header.



* Visibility: **protected**




### _readHeader
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_readHeader()
```
##### Read the response header.



* Visibility: **protected**




### prepare
```php
    \PhpOrient\Protocols\Binary\Abstracts\Operation PhpOrient\Protocols\Binary\Abstracts\Operation::prepare()
```
##### Build the operation payload



* Visibility: **public**




### send
```php
    \PhpOrient\Protocols\Binary\Abstracts\Operation PhpOrient\Protocols\Binary\Abstracts\Operation::send()
```
##### Send message to orient server



* Visibility: **public**




### _dump_streams
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_dump_streams()
```
##### Log of input/output stream



* Visibility: **protected**




### getResponse
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::getResponse()
```
##### Get Response from Server



* Visibility: **public**




### _writeByte
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeByte(integer $value)
```
##### Write a byte to the socket.



* Visibility: **protected**


##### Arguments
* $value **integer**



### _readByte
```php
    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readByte()
```
##### Read a byte from the socket.



* Visibility: **protected**




### _writeChar
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeChar(string $value)
```
##### Write a character to the socket.



* Visibility: **protected**


##### Arguments
* $value **string**



### _readChar
```php
    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readChar()
```
##### Read a character from the socket.



* Visibility: **protected**




### _writeBoolean
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeBoolean(boolean $value)
```
##### Write a boolean to the socket.



* Visibility: **protected**


##### Arguments
* $value **boolean**



### _readBoolean
```php
    boolean PhpOrient\Protocols\Binary\Abstracts\Operation::_readBoolean()
```
##### Read a boolean from the socket.



* Visibility: **protected**




### _writeShort
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeShort(integer $value)
```
##### Write a short to the socket.



* Visibility: **protected**


##### Arguments
* $value **integer**



### _readShort
```php
    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readShort()
```
##### Read a short from the socket.



* Visibility: **protected**




### _writeInt
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeInt(integer $value)
```
##### Write an integer to the socket.



* Visibility: **protected**


##### Arguments
* $value **integer**



### _readInt
```php
    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readInt()
```
##### Read an integer from the socket.



* Visibility: **protected**




### _writeLong
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeLong(integer $value)
```
##### Write a long to the socket.



* Visibility: **protected**


##### Arguments
* $value **integer**



### _readLong
```php
    integer PhpOrient\Protocols\Binary\Abstracts\Operation::_readLong()
```
##### Read a long from the socket.



* Visibility: **protected**




### _writeString
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeString(string $value)
```
##### Write a string to the socket.



* Visibility: **protected**


##### Arguments
* $value **string**



### _readString
```php
    string|null PhpOrient\Protocols\Binary\Abstracts\Operation::_readString()
```
##### Read a string from the socket.



* Visibility: **protected**




### _writeBytes
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_writeBytes(string $value)
```
##### Write bytes to the socket.



* Visibility: **protected**


##### Arguments
* $value **string**



### _readBytes
```php
    string|null PhpOrient\Protocols\Binary\Abstracts\Operation::_readBytes()
```
##### Read bytes from the socket.



* Visibility: **protected**




### _readError
```php
    \PhpOrient\Exceptions\PhpOrientException PhpOrient\Protocols\Binary\Abstracts\Operation::_readError()
```
##### Read an error from the remote server and turn it into an exception.



* Visibility: **protected**




### _readSerialized
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\Operation::_readSerialized()
```
##### Read a serialized object from the remote server.



* Visibility: **protected**




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




### _read_prefetch_record
```php
    array<mixed,\PhpOrient\Protocols\Binary\Data\Record> PhpOrient\Protocols\Binary\Abstracts\Operation::_read_prefetch_record()
```
##### Read pre-fetched and async Records



* Visibility: **protected**




### _read_sync
```php
    array|null PhpOrient\Protocols\Binary\Abstracts\Operation::_read_sync()
```
##### Read sync command payloads



* Visibility: **public**




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


##### Arguments
* $options **array** <p>The options for the newly created class instance.</p>


