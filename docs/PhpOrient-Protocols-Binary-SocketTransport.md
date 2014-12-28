PhpOrient\Protocols\Binary\SocketTransport
===============






* Class name: SocketTransport
* Namespace: PhpOrient\Protocols\Binary
* Parent class: [PhpOrient\Protocols\Common\AbstractTransport](PhpOrient-Protocols-Common-AbstractTransport.md)





Properties
----------


### $inTransaction

    public boolean $inTransaction = false

If a transaction started



* Visibility: **public**


### $databaseOpened

    public boolean $databaseOpened = false

Flag needed to know if a database is opened or not



* Visibility: **public**


### $connected

    public boolean $connected = false

Flag needed to know if connected to the server



* Visibility: **public**


### $_socket

    protected \PhpOrient\Protocols\Binary\OrientSocket $_socket





* Visibility: **protected**


### $sessionId

    protected integer $sessionId = -1





* Visibility: **protected**


### $token

    protected string $token = ''





* Visibility: **protected**


### $_protocolVersion

    protected integer $_protocolVersion





* Visibility: **protected**


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


### getProtocolVersion

    integer PhpOrient\Protocols\Binary\SocketTransport::getProtocolVersion()

Gets the version of negotiated protocol



* Visibility: **public**




### setProtocolVersion

    mixed PhpOrient\Protocols\Binary\SocketTransport::setProtocolVersion(integer $protocolVersion)





* Visibility: **public**


#### Arguments
* $protocolVersion **integer**



### getSessionId

    integer PhpOrient\Protocols\Binary\SocketTransport::getSessionId()

Gets the session ID for current connection



* Visibility: **public**




### setSessionId

    mixed PhpOrient\Protocols\Binary\SocketTransport::setSessionId($sessionId)





* Visibility: **public**


#### Arguments
* $sessionId **mixed**



### getToken

    string PhpOrient\Protocols\Binary\SocketTransport::getToken()





* Visibility: **public**




### setToken

    mixed PhpOrient\Protocols\Binary\SocketTransport::setToken(string $token)





* Visibility: **public**


#### Arguments
* $token **string**



### getSocket

    \PhpOrient\Protocols\Binary\OrientSocket PhpOrient\Protocols\Binary\SocketTransport::getSocket()

Gets the Socket, and establishes the connection if required.



* Visibility: **public**




### execute

    mixed PhpOrient\Protocols\Common\TransportInterface::execute(string $operation, array $params)

Execute the operation with the given name.



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\TransportInterface](PhpOrient-Protocols-Common-TransportInterface.md)


#### Arguments
* $operation **string** - &lt;p&gt;The operation to prepare.&lt;/p&gt;
* $params **array** - &lt;p&gt;The parameters for the operation.&lt;/p&gt;



### operationFactory

    \PhpOrient\Protocols\Binary\Abstracts\Operation PhpOrient\Protocols\Binary\SocketTransport::operationFactory(\PhpOrient\Protocols\Binary\Abstracts\Operation|string $operation, array $params)





* Visibility: **protected**


#### Arguments
* $operation **[PhpOrient\Protocols\Binary\Abstracts\Operation](PhpOrient-Protocols-Binary-Abstracts-Operation.md)|string** - &lt;p&gt;The operation name or instance.&lt;/p&gt;
* $params **array** - &lt;p&gt;The parameters for the operation.&lt;/p&gt;



### _hexDump

    string|null PhpOrient\Protocols\Binary\SocketTransport::_hexDump(string $data, boolean $htmlOutput, boolean $uppercase)

View any string as a hexDump.

This is most commonly used to view binary data from streams
or sockets while debugging, but can be used to view any string
with non-viewable characters.

* Visibility: **public**
* This method is **static**.


#### Arguments
* $data **string** - &lt;p&gt;The string to be dumped&lt;/p&gt;
* $htmlOutput **boolean** - &lt;p&gt;Set to false for non-HTML output&lt;/p&gt;
* $uppercase **boolean** - &lt;p&gt;Set to true for uppercase hex&lt;/p&gt;



### hexDump

    mixed PhpOrient\Protocols\Binary\SocketTransport::hexDump($message)

Dump data stream to HexDec format



* Visibility: **public**


#### Arguments
* $message **mixed**



### __construct

    mixed PhpOrient\Protocols\Common\AbstractTransport::__construct()

Class Constructor



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\AbstractTransport](PhpOrient-Protocols-Common-AbstractTransport.md)




### debug

    mixed PhpOrient\Protocols\Common\AbstractTransport::debug($message)





* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\AbstractTransport](PhpOrient-Protocols-Common-AbstractTransport.md)


#### Arguments
* $message **mixed**



### getClusterMap

    \PhpOrient\Protocols\Common\ClusterMap PhpOrient\Protocols\Common\AbstractTransport::getClusterMap()





* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\AbstractTransport](PhpOrient-Protocols-Common-AbstractTransport.md)




### setClusterMap

    mixed PhpOrient\Protocols\Common\AbstractTransport::setClusterMap(\PhpOrient\Protocols\Common\ClusterMap $clusterList)





* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\AbstractTransport](PhpOrient-Protocols-Common-AbstractTransport.md)


#### Arguments
* $clusterList **[PhpOrient\Protocols\Common\ClusterMap](PhpOrient-Protocols-Common-ClusterMap.md)**



### getTransaction

    \PhpOrient\Protocols\Binary\Transaction\TxCommit PhpOrient\Protocols\Common\AbstractTransport::getTransaction()

Retrieve a new transaction instance



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\AbstractTransport](PhpOrient-Protocols-Common-AbstractTransport.md)




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
* This method is defined by [PhpOrient\Protocols\Common\AbstractTransport](PhpOrient-Protocols-Common-AbstractTransport.md)


#### Arguments
* $options **array** - &lt;p&gt;The options for the newly created class instance.&lt;/p&gt;


