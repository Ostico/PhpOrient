PhpOrient\Protocols\Binary\Data\Record
===============






* Class name: Record
* Namespace: PhpOrient\Protocols\Binary\Data
* This class implements: ArrayAccess, JsonSerializable, [PhpOrient\Protocols\Binary\Abstracts\SerializableInterface](PhpOrient-Protocols-Binary-Abstracts-SerializableInterface)




Properties
----------


#### $rid
```php
    protected \PhpOrient\Protocols\Binary\Data\ID $rid
```
 



* Visibility: **protected**


#### $oClass
```php
    protected string $oClass
```
 



* Visibility: **protected**


#### $version
```php
    protected integer $version
```
 



* Visibility: **protected**


#### $oData
```php
    protected array $oData = array()
```
 



* Visibility: **protected**


Methods
-------


### getRid
```php
    \PhpOrient\Protocols\Binary\Data\ID PhpOrient\Protocols\Binary\Data\Record::getRid()
```
##### Gets the Record ID



* Visibility: **public**




### setRid
```php
    \PhpOrient\Protocols\Binary\Data\Record PhpOrient\Protocols\Binary\Data\Record::setRid(\PhpOrient\Protocols\Binary\Data\ID $rid)
```
##### Sets the Record Id



* Visibility: **public**


##### Arguments
* $rid **[PhpOrient\Protocols\Binary\Data\ID](PhpOrient-Protocols-Binary-Data-ID)**



### setOClass
```php
    \PhpOrient\Protocols\Binary\Data\Record PhpOrient\Protocols\Binary\Data\Record::setOClass(string $oClass)
```
##### Sets the Orient Class



* Visibility: **public**


##### Arguments
* $oClass **string**



### getOClass
```php
    string|null PhpOrient\Protocols\Binary\Data\Record::getOClass()
```
##### Gets the Orient Class



* Visibility: **public**




### setVersion
```php
    \PhpOrient\Protocols\Binary\Data\Record PhpOrient\Protocols\Binary\Data\Record::setVersion(integer $version)
```
##### Sets the Version



* Visibility: **public**


##### Arguments
* $version **integer**



### getVersion
```php
    integer PhpOrient\Protocols\Binary\Data\Record::getVersion()
```
##### Gets the Version



* Visibility: **public**




### setOData
```php
    \PhpOrient\Protocols\Binary\Data\Record PhpOrient\Protocols\Binary\Data\Record::setOData(array $oData)
```
##### Sets the Orient Record Content



* Visibility: **public**


##### Arguments
* $oData **array**



### getOData
```php
    string PhpOrient\Protocols\Binary\Data\Record::getOData()
```
##### Gets the Orient Record Content



* Visibility: **public**




### recordSerialize
```php
    mixed PhpOrient\Protocols\Binary\Abstracts\SerializableInterface::recordSerialize()
```
##### Return a representation of the class that can be serialized as an
OrientDB record.



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Binary\Abstracts\SerializableInterface](PhpOrient-Protocols-Binary-Abstracts-SerializableInterface)




### jsonSerialize
```php
    mixed PhpOrient\Protocols\Binary\Data\Record::jsonSerialize()
```
##### (PHP 5 &gt;= 5.4.0)<br/>
Specify data which should be serialized to JSON



* Visibility: **public**




### __toString
```php
    string PhpOrient\Protocols\Binary\Data\Record::__toString()
```
##### To String ( as alias of json_encode )



* Visibility: **public**




### offsetExists
```php
    boolean PhpOrient\Protocols\Binary\Data\Record::offsetExists(mixed $offset)
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
    mixed PhpOrient\Protocols\Binary\Data\Record::offsetGet(mixed $offset)
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
    void PhpOrient\Protocols\Binary\Data\Record::offsetSet(mixed $offset, mixed $value)
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
    void PhpOrient\Protocols\Binary\Data\Record::offsetUnset(mixed $offset)
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Offset to unset



* Visibility: **public**


##### Arguments
* $offset **mixed** <p>
                     The offset to unset.
                     </p>



### __get
```php
    mixed PhpOrient\Protocols\Binary\Data\Record::__get($name)
```
##### Magic Method, access directly to the Orient Record
content as property



* Visibility: **public**


##### Arguments
* $name **mixed**



### __set
```php
    mixed PhpOrient\Protocols\Binary\Data\Record::__set($name, $value)
```
##### Magic Method, set directly to the Orient Record
content as property



* Visibility: **public**


##### Arguments
* $name **mixed**
* $value **mixed**



### configure
```php
    \PhpOrient\Protocols\Binary\Data\Record PhpOrient\Protocols\Binary\Data\Record::configure(array $options)
```
##### Configure the object.



* Visibility: **public**


##### Arguments
* $options **array** <p>The options for the object.</p>



### fromConfig
```php
    static PhpOrient\Protocols\Binary\Data\Record::fromConfig(array $options)
```
##### Return a new class instance configured from the given options.



* Visibility: **public**
* This method is **static**.


##### Arguments
* $options **array** <p>The options for the newly created class instance.</p>


