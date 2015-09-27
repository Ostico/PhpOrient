PhpOrient\Protocols\Common\OrientNode
===============






* Class name: OrientNode
* Namespace: PhpOrient\Protocols\Common





Properties
----------


#### $name
```php
    public string $name
```
 



* Visibility: **public**


#### $id
```php
    public integer $id
```
 



* Visibility: **public**


#### $startedOn
```php
    public \DateTime $startedOn
```
 



* Visibility: **public**


#### $host
```php
    public string $host
```
 



* Visibility: **public**


#### $port
```php
    public integer $port
```
 



* Visibility: **public**


Methods
-------


### __construct
```php
    \PhpOrient\Protocols\Common\OrientNode PhpOrient\Protocols\Common\OrientNode::__construct(\PhpOrient\Protocols\Binary\Data\Record|null $node_list)
```
##### 



* Visibility: **public**


##### Arguments
* $node_list **[PhpOrient\Protocols\Binary\Data\Record](PhpOrient-Protocols-Binary-Data-Record)|null** <p>an Array with starting configs (usually from a db_open, db_reload record response or server pushes)</p>



### __toString
```php
    mixed PhpOrient\Protocols\Common\OrientNode::__toString()
```
##### 



* Visibility: **public**



