PhpOrient\Client
===============

Class Client




* Class name: Client
* Namespace: PhpOrient
* This class implements: [PhpOrient\Protocols\Common\ConfigurableInterface](PhpOrient-Protocols-Common-ConfigurableInterface.md)




Properties
----------


### $hostname

    public string $hostname

The server host.



* Visibility: **public**


### $port

    public string $port

The port for the server.



* Visibility: **public**


### $username

    public string $username

The username for the server.



* Visibility: **public**


### $password

    public string $password

The password for the server.



* Visibility: **public**


### $_transport

    protected \PhpOrient\Protocols\Common\TransportInterface $_transport

The transport to use for the connection to the server.



* Visibility: **protected**


Methods
-------


### __construct

    mixed PhpOrient\Client::__construct(string $hostname, string $port)

Class Constructor



* Visibility: **public**


#### Arguments
* $hostname **string** - &lt;p&gt;The server host.&lt;/p&gt;
* $port **string** - &lt;p&gt;The server port.&lt;/p&gt;



### setTransport

    \PhpOrient\Client PhpOrient\Client::setTransport(\PhpOrient\Protocols\Common\TransportInterface $transport)

Sets the transport



* Visibility: **public**


#### Arguments
* $transport **[PhpOrient\Protocols\Common\TransportInterface](PhpOrient-Protocols-Common-TransportInterface.md)**



### getTransport

    \PhpOrient\Protocols\Common\AbstractTransport PhpOrient\Client::getTransport()

Gets the transport



* Visibility: **public**




### getTransactionStatement

    \PhpOrient\Protocols\Binary\Transaction\TxCommit PhpOrient\Client::getTransactionStatement()

Start a new Transaction



* Visibility: **public**




### createTransport

    \PhpOrient\Protocols\Binary\SocketTransport PhpOrient\Client::createTransport(\PhpOrient\Protocols\Common\TransportInterface|null $transport)

Create a transport instance.



* Visibility: **protected**


#### Arguments
* $transport **[PhpOrient\Protocols\Common\TransportInterface](PhpOrient-Protocols-Common-TransportInterface.md)|null**



### execute

    mixed PhpOrient\Client::execute(string $operation, array $params)

Execute the given operation.



* Visibility: **public**


#### Arguments
* $operation **string** - &lt;p&gt;The name of the operation to prepare.&lt;/p&gt;
* $params **array** - &lt;p&gt;The parameters for the operation.&lt;/p&gt;



### connect

    mixed PhpOrient\Client::connect(string $username, string $password, string $serializationType)

This is the first operation requested by the client when<br />
it needs to work with the server instance.<br />
It returns the session id of the client.



* Visibility: **public**


#### Arguments
* $username **string**
* $password **string**
* $serializationType **string**



### command

    mixed PhpOrient\Client::command(string $query)

Execute a not idempotent SQL command



* Visibility: **public**


#### Arguments
* $query **string**



### query

    mixed PhpOrient\Client::query(string $query, integer $limit, string $fetchPlan)

Execute an idempotent SQL command ( Select usually )



* Visibility: **public**


#### Arguments
* $query **string**
* $limit **integer**
* $fetchPlan **string**



### queryAsync

    mixed PhpOrient\Client::queryAsync(string $query, Array $params)

Execute an idempotent SQL command in Async mode( Select usually )<br />
A callback function is needed



* Visibility: **public**


#### Arguments
* $query **string**
* $params **Array**



### sqlBatch

    mixed PhpOrient\Client::sqlBatch(string $param)

Execute an SQL Batch Script command<br />



* Visibility: **public**


#### Arguments
* $param **string**



### recordUpdate

    \PhpOrient\Protocols\Binary\Operations\RecordUpdate|\PhpOrient\Protocols\Binary\Data\Record PhpOrient\Client::recordUpdate(\PhpOrient\Protocols\Binary\Data\Record $record)

Update a Record



* Visibility: **public**


#### Arguments
* $record **[PhpOrient\Protocols\Binary\Data\Record](PhpOrient-Protocols-Binary-Data-Record.md)**



### recordCreate

    \PhpOrient\Protocols\Binary\Operations\RecordCreate|\PhpOrient\Protocols\Binary\Data\Record PhpOrient\Client::recordCreate(\PhpOrient\Protocols\Binary\Data\Record $record)

Create a record



* Visibility: **public**


#### Arguments
* $record **[PhpOrient\Protocols\Binary\Data\Record](PhpOrient-Protocols-Binary-Data-Record.md)**



### recordDelete

    \PhpOrient\Protocols\Binary\Operations\RecordDelete|\PhpOrient\Protocols\Binary\Data\Record PhpOrient\Client::recordDelete(\PhpOrient\Protocols\Binary\Data\ID $rid)

Delete a Record



* Visibility: **public**


#### Arguments
* $rid **[PhpOrient\Protocols\Binary\Data\ID](PhpOrient-Protocols-Binary-Data-ID.md)**



### recordLoad

    \PhpOrient\RecordLoad/Record PhpOrient\Client::recordLoad(\PhpOrient\Protocols\Binary\Data\ID $rid, string $fetchPlan)

Load a Record



* Visibility: **public**


#### Arguments
* $rid **[PhpOrient\Protocols\Binary\Data\ID](PhpOrient-Protocols-Binary-Data-ID.md)**
* $fetchPlan **string**



### dbSize

    integer|string PhpOrient\Client::dbSize()

Get the size of a Database



* Visibility: **public**




### dbReload

    \PhpOrient\Protocols\Common\ClusterMap PhpOrient\Client::dbReload()

Reload the structure of a Database



* Visibility: **public**




### dbRelease

    mixed PhpOrient\Client::dbRelease(string $db_name, string $storage_type)

Release the structure of a Database



* Visibility: **public**


#### Arguments
* $db_name **string**
* $storage_type **string**



### dbOpen

    \PhpOrient\Protocols\Common\ClusterMap PhpOrient\Client::dbOpen(string $database, string $username, string $password, string $dbType)

Open a Database and perform a connection<br />
if it is not established before



* Visibility: **public**


#### Arguments
* $database **string**
* $username **string**
* $password **string**
* $dbType **string**



### dbList

    array PhpOrient\Client::dbList()

List all databases inside OrientDB instance



* Visibility: **public**




### dbFreeze

    boolean PhpOrient\Client::dbFreeze(string $db_name, string $storage_type)

Freeze a database ( need Release to unlock )<br />
Flushes all cached content to the disk storage and allows to perform only read commands



* Visibility: **public**


#### Arguments
* $db_name **string**
* $storage_type **string**



### dbExists

    boolean PhpOrient\Client::dbExists($database, string $database_type)

Check if a database exists



* Visibility: **public**


#### Arguments
* $database **mixed**
* $database_type **string**



### dbDrop

    mixed PhpOrient\Client::dbDrop($database, string $storage_type)

Drop an existent database



* Visibility: **public**


#### Arguments
* $database **mixed**
* $storage_type **string**



### dbCreate

    boolean PhpOrient\Client::dbCreate(string $database, string $storage_type, string $database_type)

Create a new Database



* Visibility: **public**


#### Arguments
* $database **string**
* $storage_type **string**
* $database_type **string**



### dbCountRecords

    integer|string PhpOrient\Client::dbCountRecords()

Create a new Database



* Visibility: **public**




### dbClose

    integer PhpOrient\Client::dbClose()

Close a database a drop the connection



* Visibility: **public**




### dataClusterDrop

    boolean PhpOrient\Client::dataClusterDrop(array $params)

Drop a data cluster



* Visibility: **public**


#### Arguments
* $params **array**



### dataClusterDataRange

    array<mixed,integer>|array<mixed,string> PhpOrient\Client::dataClusterDataRange(array $params)

Returns the range of record ids for a cluster.



* Visibility: **public**


#### Arguments
* $params **array**



### dataClusterCount

    integer|string PhpOrient\Client::dataClusterCount(array $params)

Returns the number of records in one or more clusters.



* Visibility: **public**


#### Arguments
* $params **array**



### dataClusterAdd

    integer PhpOrient\Client::dataClusterAdd(array $params)

Add a new data cluster



* Visibility: **public**


#### Arguments
* $params **array**



### configure

    \PhpOrient\Protocols\Common\ConfigurableInterface PhpOrient\Protocols\Common\ConfigurableInterface::configure(array $options)

Configure the object.



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\ConfigurableInterface](PhpOrient-Protocols-Common-ConfigurableInterface.md)


#### Arguments
* $options **array** - &lt;p&gt;The options for the object.&lt;/p&gt;



### fromConfig

    static PhpOrient\Client::fromConfig(array $options)

Return a new class instance configured from the given options.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $options **array** - &lt;p&gt;The options for the newly created class instance.&lt;/p&gt;


