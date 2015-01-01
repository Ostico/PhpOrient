PhpOrient\Protocols\Binary\Stream\Writer
===============






* Class name: Writer
* Namespace: PhpOrient\Protocols\Binary\Stream







Methods
-------


### packByte
```php
    string PhpOrient\Protocols\Binary\Stream\Writer::packByte(integer $value)
```
##### Pack a byte.



* Visibility: **public**
* This method is **static**.


##### Arguments
* $value **integer**



### packShort
```php
    string PhpOrient\Protocols\Binary\Stream\Writer::packShort(integer $value)
```
##### Pack a short.



* Visibility: **public**
* This method is **static**.


##### Arguments
* $value **integer**



### packLong
```php
    string PhpOrient\Protocols\Binary\Stream\Writer::packLong(integer|string $value)
```
##### Pack a long.

If it is a 32bit PHP we suppose that this log is treated by bcmath
TODO 32bit

* Visibility: **public**
* This method is **static**.


##### Arguments
* $value **integer|string**



### str2bin
```php
    array PhpOrient\Protocols\Binary\Stream\Writer::str2bin($value)
```
##### Transform an arbitrary precision number ( string )
to a binary string of bits and take the remainder also



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $value **mixed**



### packInt
```php
    string PhpOrient\Protocols\Binary\Stream\Writer::packInt(integer $value)
```
##### Pack an integer.



* Visibility: **public**
* This method is **static**.


##### Arguments
* $value **integer**



### packString
```php
    string PhpOrient\Protocols\Binary\Stream\Writer::packString(string $value)
```
##### Pack a string.



* Visibility: **public**
* This method is **static**.


##### Arguments
* $value **string**



### packBytes
```php
    string PhpOrient\Protocols\Binary\Stream\Writer::packBytes(string $value)
```
##### Pack bytes.



* Visibility: **public**
* This method is **static**.


##### Arguments
* $value **string**


