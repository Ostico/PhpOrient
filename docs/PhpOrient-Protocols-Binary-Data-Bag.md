PhpOrient\Protocols\Binary\Data\Bag
===============






* Class name: Bag
* Namespace: PhpOrient\Protocols\Binary\Data
* This class implements: Countable, ArrayAccess, Iterator


Constants
----------


### EMBEDDED

    const EMBEDDED = 0





### TREE

    const TREE = 1





Properties
----------


### $serialized

    protected string $serialized





* Visibility: **protected**


### $deserialized

    protected string $deserialized





* Visibility: **protected**


### $type

    public integer $type





* Visibility: **public**


### $size

    public integer $size





* Visibility: **public**


### $uuid

    protected string $uuid





* Visibility: **protected**


### $fileId

    protected integer $fileId





* Visibility: **protected**


### $pageIndex

    protected integer $pageIndex





* Visibility: **protected**


### $pageOffset

    protected integer $pageOffset





* Visibility: **protected**


### $changeSize

    protected integer $changeSize





* Visibility: **protected**


### $ReaderOffset

    protected integer $ReaderOffset





* Visibility: **protected**


### $baseOffset

    protected integer $baseOffset





* Visibility: **protected**


### $offset

    protected integer $offset





* Visibility: **protected**


### $items

    protected array $items = array()





* Visibility: **protected**


Methods
-------


### __construct

    mixed PhpOrient\Protocols\Binary\Data\Bag::__construct(string $serialized)

# RIDBag Constructor



* Visibility: **public**


#### Arguments
* $serialized **string** - &lt;p&gt;the base64 encoded bag&lt;/p&gt;



### getType

    integer PhpOrient\Protocols\Binary\Data\Bag::getType()

Gets the Type



* Visibility: **public**




### getSize

    integer PhpOrient\Protocols\Binary\Data\Bag::getSize()

Gets the Size



* Visibility: **public**




### parse

    mixed PhpOrient\Protocols\Binary\Data\Bag::parse()

Parse the bag header.



* Visibility: **protected**




### parseEmbedded

    mixed PhpOrient\Protocols\Binary\Data\Bag::parseEmbedded()

Parse the header for an embedded bag.



* Visibility: **protected**




### parseTree

    mixed PhpOrient\Protocols\Binary\Data\Bag::parseTree()

Parse the header for a tree bag.



* Visibility: **protected**




### current

    mixed PhpOrient\Protocols\Binary\Data\Bag::current()

(PHP 5 &gt;= 5.0.0)<br/>
Return the current element



* Visibility: **public**




### next

    void PhpOrient\Protocols\Binary\Data\Bag::next()

(PHP 5 &gt;= 5.0.0)<br/>
Move forward to next element



* Visibility: **public**




### key

    mixed PhpOrient\Protocols\Binary\Data\Bag::key()

(PHP 5 &gt;= 5.0.0)<br/>
Return the key of the current element



* Visibility: **public**




### valid

    boolean PhpOrient\Protocols\Binary\Data\Bag::valid()

(PHP 5 &gt;= 5.0.0)<br/>
Checks if current position is valid



* Visibility: **public**




### rewind

    void PhpOrient\Protocols\Binary\Data\Bag::rewind()

(PHP 5 &gt;= 5.0.0)<br/>
Rewind the Iterator to the first element



* Visibility: **public**




### offsetExists

    boolean PhpOrient\Protocols\Binary\Data\Bag::offsetExists(mixed $offset)

(PHP 5 &gt;= 5.0.0)<br/>
Whether a offset exists



* Visibility: **public**


#### Arguments
* $offset **mixed** - &lt;p&gt;
                     An offset to check for.
                     &lt;/p&gt;



### offsetGet

    \PhpOrient\Protocols\Binary\Data\ID PhpOrient\Protocols\Binary\Data\Bag::offsetGet(mixed $offset)

(PHP 5 &gt;= 5.0.0)<br/>
Offset to retrieve



* Visibility: **public**


#### Arguments
* $offset **mixed** - &lt;p&gt;
                     The offset to retrieve.
                     &lt;/p&gt;



### offsetSet

    void PhpOrient\Protocols\Binary\Data\Bag::offsetSet(mixed $offset, mixed $value)

(PHP 5 &gt;= 5.0.0)<br/>
Offset to set



* Visibility: **public**


#### Arguments
* $offset **mixed** - &lt;p&gt;
                     The offset to assign the value to.
                     &lt;/p&gt;
* $value **mixed** - &lt;p&gt;
                     The value to set.
                     &lt;/p&gt;



### offsetUnset

    void PhpOrient\Protocols\Binary\Data\Bag::offsetUnset(mixed $offset)

(PHP 5 &gt;= 5.0.0)<br/>
Offset to unset



* Visibility: **public**


#### Arguments
* $offset **mixed** - &lt;p&gt;
                     The offset to unset.
                     &lt;/p&gt;



### count

    integer PhpOrient\Protocols\Binary\Data\Bag::count()

(PHP 5 &gt;= 5.1.0)<br/>
Count elements of an object



* Visibility: **public**



