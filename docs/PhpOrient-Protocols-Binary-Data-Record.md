PhpOrient\Protocols\Binary\Data\Record
===============






* Class name: Record
* Namespace: PhpOrient\Protocols\Binary\Data
* This class implements: ArrayAccess, JsonSerializable, [PhpOrient\Protocols\Binary\Abstracts\SerializableInterface](PhpOrient-Protocols-Binary-Abstracts-SerializableInterface.md)




Properties
----------


### $rid

    protected \PhpOrient\Protocols\Binary\Data\ID $rid





* Visibility: **protected**


### $oClass

    protected string $oClass





* Visibility: **protected**


### $version

    protected integer $version





* Visibility: **protected**


### $oData

    protected array $oData = array()





* Visibility: **protected**


Methods
-------


### getRid

    \PhpOrient\Protocols\Binary\Data\ID PhpOrient\Protocols\Binary\Data\Record::getRid()

Gets the Record ID



* Visibility: **public**




### setRid

    \PhpOrient\Protocols\Binary\Data\Record PhpOrient\Protocols\Binary\Data\Record::setRid(\PhpOrient\Protocols\Binary\Data\ID $rid)

Sets the Record Id



* Visibility: **public**


#### Arguments
* $rid **[PhpOrient\Protocols\Binary\Data\ID](PhpOrient-Protocols-Binary-Data-ID.md)**



### setOClass

    \PhpOrient\Protocols\Binary\Data\Record PhpOrient\Protocols\Binary\Data\Record::setOClass(string $oClass)

Sets the Orient Class



* Visibility: **public**


#### Arguments
* $oClass **string**



### getOClass

    string|null PhpOrient\Protocols\Binary\Data\Record::getOClass()

Gets the Orient Class



* Visibility: **public**




### setVersion

    \PhpOrient\Protocols\Binary\Data\Record PhpOrient\Protocols\Binary\Data\Record::setVersion(integer $version)

Sets the Version



* Visibility: **public**


#### Arguments
* $version **integer**



### getVersion

    integer PhpOrient\Protocols\Binary\Data\Record::getVersion()

Gets the Version



* Visibility: **public**




### setOData

    \PhpOrient\Protocols\Binary\Data\Record PhpOrient\Protocols\Binary\Data\Record::setOData(array $oData)

Sets the Orient Record Content



* Visibility: **public**


#### Arguments
* $oData **array**



### getOData

    string PhpOrient\Protocols\Binary\Data\Record::getOData()

Gets the Orient Record Content



* Visibility: **public**




### recordSerialize

    mixed PhpOrient\Protocols\Binary\Abstracts\SerializableInterface::recordSerialize()

Return a representation of the class that can be serialized as an
OrientDB record.



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\SerializableInterface](PhpOrient-Protocols-Binary-Abstracts-SerializableInterface.md)




### jsonSerialize

    mixed PhpOrient\Protocols\Binary\Data\Record::jsonSerialize()

(PHP 5 &gt;= 5.4.0)<br/>
Specify data which should be serialized to JSON



* Visibility: **public**




### __toString

    string PhpOrient\Protocols\Binary\Data\Record::__toString()

To String ( as alias of json_encode )



* Visibility: **public**




### offsetExists

    boolean PhpOrient\Protocols\Binary\Data\Record::offsetExists(mixed $offset)

(PHP 5 &gt;= 5.0.0)<br/>
Whether a offset exists



* Visibility: **public**


#### Arguments
* $offset **mixed** - &lt;p&gt;
                     An offset to check for.
                     &lt;/p&gt;



### offsetGet

    mixed PhpOrient\Protocols\Binary\Data\Record::offsetGet(mixed $offset)

(PHP 5 &gt;= 5.0.0)<br/>
Offset to retrieve



* Visibility: **public**


#### Arguments
* $offset **mixed** - &lt;p&gt;
                     The offset to retrieve.
                     &lt;/p&gt;



### offsetSet

    void PhpOrient\Protocols\Binary\Data\Record::offsetSet(mixed $offset, mixed $value)

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

    void PhpOrient\Protocols\Binary\Data\Record::offsetUnset(mixed $offset)

(PHP 5 &gt;= 5.0.0)<br/>
Offset to unset



* Visibility: **public**


#### Arguments
* $offset **mixed** - &lt;p&gt;
                     The offset to unset.
                     &lt;/p&gt;



### __get

    mixed PhpOrient\Protocols\Binary\Data\Record::__get($name)

Magic Method, access directly to the Orient Record
content as property



* Visibility: **public**


#### Arguments
* $name **mixed**



### __set

    mixed PhpOrient\Protocols\Binary\Data\Record::__set($name, $value)

Magic Method, set directly to the Orient Record
content as property



* Visibility: **public**


#### Arguments
* $name **mixed**
* $value **mixed**



### configure

    \PhpOrient\Protocols\Binary\Data\Record PhpOrient\Protocols\Binary\Data\Record::configure(array $options)

Configure the object.



* Visibility: **public**


#### Arguments
* $options **array** - &lt;p&gt;The options for the object.&lt;/p&gt;



### fromConfig

    static PhpOrient\Protocols\Binary\Data\Record::fromConfig(array $options)

Return a new class instance configured from the given options.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $options **array** - &lt;p&gt;The options for the newly created class instance.&lt;/p&gt;


