PhpOrient\Protocols\Binary\Data\ID
===============






* Class name: ID
* Namespace: PhpOrient\Protocols\Binary\Data
* This class implements: JsonSerializable




Properties
----------


#### $cluster
```php
    public integer $cluster
```
 The cluster the record belongs to.



* Visibility: **public**


#### $position
```php
    public integer $position
```
 The position of the record in the cluster.



* Visibility: **public**


Methods
-------


### __construct
```php
    mixed PhpOrient\Protocols\Binary\Data\ID::__construct(integer|string|array $cluster, integer $position)
```
##### # Record ID Constructor.



* Visibility: **public**


##### Arguments
* $cluster **integer|string|array** <p>The cluster id, string representation or configuration object</p>
* $position **integer** <p>The position in the cluster, if $cluster is an integer.</p>



### jsonSerialize
```php
    mixed PhpOrient\Protocols\Binary\Data\ID::jsonSerialize()
```
##### (PHP 5 &gt;= 5.4.0)<br/>
Specify data which should be serialized to JSON



* Visibility: **public**




### __toString
```php
    string PhpOrient\Protocols\Binary\Data\ID::__toString()
```
##### 



* Visibility: **public**




### parseString
```php
    array PhpOrient\Protocols\Binary\Data\ID::parseString($input)
```
##### Transform a rid string format ( '#1:2' ) to array [ cluster, position ]



* Visibility: **public**
* This method is **static**.


##### Arguments
* $input **mixed** <p>string</p>


