PhpOrient\Protocols\Common\ClusterMap
===============

Class ClusterMap




* Class name: ClusterMap
* Namespace: PhpOrient\Protocols\Common
* This class implements: [PhpOrient\Protocols\Common\ConfigurableInterface](PhpOrient-Protocols-Common-ConfigurableInterface), ArrayAccess, Countable, Iterator




Properties
----------


#### $dataClusters
```php
    protected array $dataClusters
```
 



* Visibility: **protected**


#### $reverseMap
```php
    protected array $reverseMap
```
 



* Visibility: **protected**


#### $reverseIDMap
```php
    protected array $reverseIDMap
```
 



* Visibility: **protected**


#### $servers
```php
    protected integer $servers
```
 



* Visibility: **protected**


#### $release
```php
    protected string $release
```
 



* Visibility: **protected**


#### $internal_position
```php
    protected mixed $internal_position
```
 



* Visibility: **protected**


Methods
-------


### getServers
```php
    integer PhpOrient\Protocols\Common\ClusterMap::getServers()
```
##### 



* Visibility: **public**




### getRelease
```php
    string PhpOrient\Protocols\Common\ClusterMap::getRelease()
```
##### 



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



### getIdList
```php
    array<mixed,integer>|array<mixed,string> PhpOrient\Protocols\Common\ClusterMap::getIdList()
```
##### Return the list of cluster IDs



* Visibility: **public**




### getClusterID
```php
    integer|null PhpOrient\Protocols\Common\ClusterMap::getClusterID($name)
```
##### Alias for @see ClusterList::offsetGet



* Visibility: **public**


##### Arguments
* $name **mixed**



### dropClusterID
```php
    mixed PhpOrient\Protocols\Common\ClusterMap::dropClusterID($ID)
```
##### Remove a cluster by ID



* Visibility: **public**


##### Arguments
* $ID **mixed**



### offsetExists
```php
    boolean PhpOrient\Protocols\Common\ClusterMap::offsetExists(mixed $name)
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Whether a offset exists



* Visibility: **public**


##### Arguments
* $name **mixed** <p>
                     An offset to check for.
                     </p>



### offsetGet
```php
    integer|null PhpOrient\Protocols\Common\ClusterMap::offsetGet(mixed $name)
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Offset to retrieve



* Visibility: **public**


##### Arguments
* $name **mixed** <p>
                     The offset to retrieve.
                     </p>



### offsetSet
```php
    void PhpOrient\Protocols\Common\ClusterMap::offsetSet(mixed $name, mixed $value)
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Offset to set



* Visibility: **public**


##### Arguments
* $name **mixed** <p>
                     The offset to assign the value to.
                     </p>
* $value **mixed** <p>
                     The value to set.
                     </p>



### offsetUnset
```php
    void PhpOrient\Protocols\Common\ClusterMap::offsetUnset(mixed $name)
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Offset to unset



* Visibility: **public**


##### Arguments
* $name **mixed** <p>
                     The offset to unset.
                     </p>



### count
```php
    integer PhpOrient\Protocols\Common\ClusterMap::count()
```
##### (PHP 5 &gt;= 5.1.0)<br/>
Count elements of an object



* Visibility: **public**




### next
```php
    void PhpOrient\Protocols\Common\ClusterMap::next()
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Move forward to next element



* Visibility: **public**




### key
```php
    mixed PhpOrient\Protocols\Common\ClusterMap::key()
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Return the key of the current element



* Visibility: **public**




### valid
```php
    boolean PhpOrient\Protocols\Common\ClusterMap::valid()
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Checks if current position is valid



* Visibility: **public**




### rewind
```php
    void PhpOrient\Protocols\Common\ClusterMap::rewind()
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Rewind the Iterator to the first element



* Visibility: **public**




### current
```php
    mixed PhpOrient\Protocols\Common\ClusterMap::current()
```
##### (PHP 5 &gt;= 5.0.0)<br/>
Return the current element



* Visibility: **public**




### fromConfig
```php
    static PhpOrient\Protocols\Common\ClusterMap::fromConfig(array $options)
```
##### Return a new class instance configured from the given options.



* Visibility: **public**
* This method is **static**.


##### Arguments
* $options **array** <p>The options for the newly created class instance.</p>


