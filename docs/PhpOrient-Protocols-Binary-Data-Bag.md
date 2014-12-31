PhpOrient\Protocols\Binary\Data\Bag
===============






* Class name: Bag
* Namespace: PhpOrient\Protocols\Binary\Data
* This class implements: Countable, ArrayAccess, Iterator


Constants
----------


### EMBEDDED
```php
    const EMBEDDED = 0
```




### TREE
```php
    const TREE = 1
```




Properties
----------


#### $serialized
```php
    protected string $serialized
```
 



* Visibility: **protected**


#### $deserialized
```php
    protected string $deserialized
```
 



* Visibility: **protected**


#### $type
```php
    public integer $type
```
 



* Visibility: **public**


#### $size
```php
    public integer $size
```
 



* Visibility: **public**


#### $uuid
```php
    protected string $uuid
```
 



* Visibility: **protected**


#### $fileId
```php
    protected integer $fileId
```
 



* Visibility: **protected**


#### $pageIndex
```php
    protected integer $pageIndex
```
 



* Visibility: **protected**


#### $pageOffset
```php
    protected integer $pageOffset
```
 



* Visibility: **protected**


#### $changeSize
```php
    protected integer $changeSize
```
 



* Visibility: **protected**


#### $ReaderOffset
```php
    protected integer $ReaderOffset
```
 



* Visibility: **protected**


#### $baseOffset
```php
    protected integer $baseOffset
```
 



* Visibility: **protected**


#### $offset
```php
    protected integer $offset
```
 



* Visibility: **protected**


#### $items
```php
    protected array $items = array()
```
 



* Visibility: **protected**


Methods
-------


### __construct
```php
    mixed PhpOrient\Protocols\Binary\Data\Bag::__construct(string $serialized)
```
##### # RIDBag Constructor



* Visibility: **public**


##### Arguments
* $serialized **string** <p>the base64 encoded bag</p>



### getType
```php
    integer PhpOrient\Protocols\Binary\Data\Bag::getType()
```
##### Gets the Type



* Visibility: **public**




### getSize
```php
    integer PhpOrient\Protocols\Binary\Data\Bag::getSize()
```
##### Gets the Size



* Visibility: **public**




### parse
```php
    mixed PhpOrient\Protocols\Binary\Data\Bag::parse()
```
##### Parse the bag header.



* Visibility: **protected**




### parseEmbedded
```php
    mixed PhpOrient\Protocols\Binary\Data\Bag::parseEmbedded()
```
##### Parse the header for an embedded bag.



* Visibility: **protected**




### parseTree
```php
    mixed PhpOrient\Protocols\Binary\Data\Bag::parseTree()
```
##### Parse the header for a tree bag.



* Visibility: **protected**




### current
```php
    mixed PhpOrient\Protocols\Binary\Data\Bag::current()
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Return the current element



* Visibility: **public**




### next
```php
    void PhpOrient\Protocols\Binary\Data\Bag::next()
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Move forward to next element



* Visibility: **public**




### key
```php
    mixed PhpOrient\Protocols\Binary\Data\Bag::key()
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Return the key of the current element



* Visibility: **public**




### valid
```php
    boolean PhpOrient\Protocols\Binary\Data\Bag::valid()
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Checks if current position is valid



* Visibility: **public**




### rewind
```php
    void PhpOrient\Protocols\Binary\Data\Bag::rewind()
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Rewind the Iterator to the first element



* Visibility: **public**




### offsetExists
```php
    boolean PhpOrient\Protocols\Binary\Data\Bag::offsetExists(mixed $offset)
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Whether a offset exists



* Visibility: **public**


##### Arguments
* $offset **mixed** <p>
                     An offset to check for.
                     </p>



### offsetGet
```php
    \PhpOrient\Protocols\Binary\Data\ID PhpOrient\Protocols\Binary\Data\Bag::offsetGet(mixed $offset)
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Offset to retrieve



* Visibility: **public**


##### Arguments
* $offset **mixed** <p>
                     The offset to retrieve.
                     </p>



### offsetSet
```php
    void PhpOrient\Protocols\Binary\Data\Bag::offsetSet(mixed $offset, mixed $value)
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Offset to set



* Visibility: **public**


##### Arguments
* $offset **mixed** <p>
                     The offset to assign the value to.
                     </p>
* $value **mixed** <p>
                     The value to set.
                     </p>



### offsetUnset
```php
    void PhpOrient\Protocols\Binary\Data\Bag::offsetUnset(mixed $offset)
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Offset to unset



* Visibility: **public**


##### Arguments
* $offset **mixed** <p>
                     The offset to unset.
                     </p>



### count
```php
    integer PhpOrient\Protocols\Binary\Data\Bag::count()
```
##### (PHP 5 &gt;= 5.1.0)<br/>
Count elements of an object



* Visibility: **public**



