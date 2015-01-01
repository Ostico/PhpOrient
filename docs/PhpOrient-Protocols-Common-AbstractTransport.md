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


#### $clusterList
```php
    protected \PhpOrient\Protocols\Common\ClusterMap $clusterList
```
 



* Visibility: **protected**


#### $_logger
```php
    protected \Psr\Log\LoggerInterface $_logger
```
 



* Visibility: **protected**
* This property is **static**.


Methods
-------


### __construct
```php
    mixed PhpOrient\Protocols\Common\AbstractTransport::__construct()
```
##### Class Constructor



* Visibility: **public**




### debug
```php
    mixed PhpOrient\Protocols\Common\AbstractTransport::debug($message)
```
##### 



* Visibility: **public**


##### Arguments
* $message **mixed**



### getClusterMap
```php
    \PhpOrient\Protocols\Common\ClusterMap PhpOrient\Protocols\Common\AbstractTransport::getClusterMap()
```
##### 



* Visibility: **public**




### setClusterMap
```php
    mixed PhpOrient\Protocols\Common\AbstractTransport::setClusterMap(\PhpOrient\Protocols\Common\ClusterMap $clusterList)
```
##### 



* Visibility: **public**


##### Arguments
* $clusterList **[PhpOrient\Protocols\Common\ClusterMap](PhpOrient-Protocols-Common-ClusterMap)**



### getTransaction
```php
    \PhpOrient\Protocols\Binary\Transaction\TxCommit PhpOrient\Protocols\Common\AbstractTransport::getTransaction()
```
##### Retrieve a new transaction instance



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


