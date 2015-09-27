PhpOrient\Protocols\Common\AbstractTransport
===============






* Class name: AbstractTransport
* Namespace: PhpOrient\Protocols\Common
* This is an **abstract** class
* This class implements: [PhpOrient\Protocols\Common\TransportInterface](PhpOrient-Protocols-Common-TransportInterface)




Properties
----------


#### $hostname
```php
    protected string $hostname
```
 



* Visibility: **protected**


#### $port
```php
    protected string $port
```
 



* Visibility: **protected**


#### $username
```php
    protected string $username
```
 



* Visibility: **protected**


#### $password
```php
    protected string $password
```
 



* Visibility: **protected**


#### $clusterMap
```php
    protected \PhpOrient\Protocols\Common\ClustersMap $clusterMap
```
 



* Visibility: **protected**


#### $nodesList
```php
    protected array<mixed,\PhpOrient\Protocols\Common\OrientNode> $nodesList
```
 



* Visibility: **protected**


#### $_logger
```php
    protected \Psr\Log\LoggerInterface $_logger
```
 



* Visibility: **protected**


#### $orientVersion
```php
    protected \PhpOrient\Protocols\Common\OrientVersion $orientVersion
```
 



* Visibility: **protected**


Methods
-------


### __construct
```php
    mixed PhpOrient\Protocols\Common\AbstractTransport::__construct()
```
##### Class Constructor



* Visibility: **public**




### setLogger
```php
    mixed PhpOrient\Protocols\Common\AbstractTransport::setLogger()
```
##### Set the client Logger



* Visibility: **public**




### getLogger
```php
    \Psr\Log\LoggerInterface PhpOrient\Protocols\Common\AbstractTransport::getLogger()
```
##### Get the Logger from transport



* Visibility: **public**




### debug
```php
    mixed PhpOrient\Protocols\Common\AbstractTransport::debug($message)
```
##### Debug method



* Visibility: **public**


##### Arguments
* $message **mixed**



### getClusterMap
```php
    \PhpOrient\Protocols\Common\ClustersMap PhpOrient\Protocols\Common\AbstractTransport::getClusterMap()
```
##### 



* Visibility: **public**




### setClustersMap
```php
    mixed PhpOrient\Protocols\Common\AbstractTransport::setClustersMap(\PhpOrient\Protocols\Common\ClustersMap $clusterList)
```
##### 



* Visibility: **public**


##### Arguments
* $clusterList **[PhpOrient\Protocols\Common\ClustersMap](PhpOrient-Protocols-Common-ClustersMap)**



### getTransaction
```php
    \PhpOrient\Protocols\Binary\Transaction\TxCommit PhpOrient\Protocols\Common\AbstractTransport::getTransaction()
```
##### Retrieve a new transaction instance



* Visibility: **public**




### getNodesList
```php
    array<mixed,\PhpOrient\Protocols\Common\OrientNode> PhpOrient\Protocols\Common\AbstractTransport::getNodesList(boolean|false $filterActualNode)
```
##### Retrieve the nodes list, optionally filter excluding the actual one

//TODO Improve with different protocol types handler if another transport protocol are implemented

* Visibility: **public**


##### Arguments
* $filterActualNode **boolean|false**



### setNodesList
```php
    mixed PhpOrient\Protocols\Common\AbstractTransport::setNodesList(array<mixed,\PhpOrient\Protocols\Common\OrientNode> $nodesList)
```
##### 



* Visibility: **public**


##### Arguments
* $nodesList **array&lt;mixed,\PhpOrient\Protocols\Common\OrientNode&gt;**



### getOrientVersion
```php
    \PhpOrient\Protocols\Common\OrientVersion PhpOrient\Protocols\Common\AbstractTransport::getOrientVersion()
```
##### 



* Visibility: **public**




### setOrientVersion
```php
    mixed PhpOrient\Protocols\Common\AbstractTransport::setOrientVersion(\PhpOrient\Protocols\Common\OrientVersion $orientVersion)
```
##### 



* Visibility: **public**


##### Arguments
* $orientVersion **[PhpOrient\Protocols\Common\OrientVersion](PhpOrient-Protocols-Common-OrientVersion)**



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
    static PhpOrient\Protocols\Common\AbstractTransport::fromConfig(array $options)
```
##### Return a new class instance configured from the given options.



* Visibility: **public**
* This method is **static**.


##### Arguments
* $options **array** <p>The options for the newly created class instance.</p>



### execute
```php
    mixed PhpOrient\Protocols\Common\TransportInterface::execute(string $operation, array $params)
```
##### Execute the operation with the given name.



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\TransportInterface](PhpOrient-Protocols-Common-TransportInterface)


##### Arguments
* $operation **string** <p>The operation to prepare.</p>
* $params **array** <p>The parameters for the operation.</p>


