PhpOrient\Protocols\Common\AbstractTransport
===============






* Class name: AbstractTransport
* Namespace: PhpOrient\Protocols\Common
* This is an **abstract** class
* This class implements: [PhpOrient\Protocols\Common\TransportInterface](PhpOrient-Protocols-Common-TransportInterface.md)




Properties
----------


### $hostname

    protected string $hostname





* Visibility: **protected**


### $port

    protected string $port





* Visibility: **protected**


### $username

    protected string $username





* Visibility: **protected**


### $password

    protected string $password





* Visibility: **protected**


### $clusterList

    protected \PhpOrient\Protocols\Common\ClusterMap $clusterList





* Visibility: **protected**


### $_logger

    protected \Psr\Log\LoggerInterface $_logger





* Visibility: **protected**
* This property is **static**.


Methods
-------


### __construct

    mixed PhpOrient\Protocols\Common\AbstractTransport::__construct()

Class Constructor



* Visibility: **public**




### debug

    mixed PhpOrient\Protocols\Common\AbstractTransport::debug($message)





* Visibility: **public**


#### Arguments
* $message **mixed**



### getClusterMap

    \PhpOrient\Protocols\Common\ClusterMap PhpOrient\Protocols\Common\AbstractTransport::getClusterMap()





* Visibility: **public**




### setClusterMap

    mixed PhpOrient\Protocols\Common\AbstractTransport::setClusterMap(\PhpOrient\Protocols\Common\ClusterMap $clusterList)





* Visibility: **public**


#### Arguments
* $clusterList **[PhpOrient\Protocols\Common\ClusterMap](PhpOrient-Protocols-Common-ClusterMap.md)**



### getTransaction

    \PhpOrient\Protocols\Binary\Transaction\TxCommit PhpOrient\Protocols\Common\AbstractTransport::getTransaction()

Retrieve a new transaction instance



* Visibility: **public**




### configure

    \PhpOrient\Protocols\Common\ConfigurableInterface PhpOrient\Protocols\Common\ConfigurableInterface::configure(array $options)

Configure the object.



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\ConfigurableInterface](PhpOrient-Protocols-Common-ConfigurableInterface.md)


#### Arguments
* $options **array** - &lt;p&gt;The options for the object.&lt;/p&gt;



### fromConfig

    static PhpOrient\Protocols\Common\AbstractTransport::fromConfig(array $options)

Return a new class instance configured from the given options.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $options **array** - &lt;p&gt;The options for the newly created class instance.&lt;/p&gt;



### execute

    mixed PhpOrient\Protocols\Common\TransportInterface::execute(string $operation, array $params)

Execute the operation with the given name.



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\TransportInterface](PhpOrient-Protocols-Common-TransportInterface.md)


#### Arguments
* $operation **string** - &lt;p&gt;The operation to prepare.&lt;/p&gt;
* $params **array** - &lt;p&gt;The parameters for the operation.&lt;/p&gt;


