PhpOrient\Protocols\Common\ClusterMap
===============

Class ClusterMap




* Class name: ClusterMap
* Namespace: PhpOrient\Protocols\Common
* This class implements: [PhpOrient\Protocols\Common\ConfigurableInterface](PhpOrient-Protocols-Common-ConfigurableInterface.md), ArrayAccess, Countable, Iterator




Properties
----------


### $dataClusters

    protected array $dataClusters





* Visibility: **protected**


### $reverseMap

    protected array $reverseMap





* Visibility: **protected**


### $reverseIDMap

    protected array $reverseIDMap





* Visibility: **protected**


### $servers

    protected integer $servers





* Visibility: **protected**


### $release

    protected string $release





* Visibility: **protected**


### $internal_position

    protected mixed $internal_position





* Visibility: **protected**


Methods
-------


### getServers

    integer PhpOrient\Protocols\Common\ClusterMap::getServers()





* Visibility: **public**




### getRelease

    string PhpOrient\Protocols\Common\ClusterMap::getRelease()





* Visibility: **public**




### configure

    \PhpOrient\Protocols\Common\ConfigurableInterface PhpOrient\Protocols\Common\ConfigurableInterface::configure(array $options)

Configure the object.



* Visibility: **public**
* This method is defined by [PhpOrient\Protocols\Common\ConfigurableInterface](PhpOrient-Protocols-Common-ConfigurableInterface.md)


#### Arguments
* $options **array** - &lt;p&gt;The options for the object.&lt;/p&gt;



### getClusterID

    integer|null PhpOrient\Protocols\Common\ClusterMap::getClusterID($name)

Alias for @see ClusterList::offsetGet



* Visibility: **public**


#### Arguments
* $name **mixed**



### dropClusterID

    mixed PhpOrient\Protocols\Common\ClusterMap::dropClusterID($ID)

Remove a cluster by ID



* Visibility: **public**


#### Arguments
* $ID **mixed**



### offsetExists

    boolean PhpOrient\Protocols\Common\ClusterMap::offsetExists(mixed $name)

(PHP 5 &gt;= 5.0.0)<br/>
Whether a offset exists



* Visibility: **public**


#### Arguments
* $name **mixed** - &lt;p&gt;
                     An offset to check for.
                     &lt;/p&gt;



### offsetGet

    integer|null PhpOrient\Protocols\Common\ClusterMap::offsetGet(mixed $name)

(PHP 5 &gt;= 5.0.0)<br/>
Offset to retrieve



* Visibility: **public**


#### Arguments
* $name **mixed** - &lt;p&gt;
                     The offset to retrieve.
                     &lt;/p&gt;



### offsetSet

    void PhpOrient\Protocols\Common\ClusterMap::offsetSet(mixed $name, mixed $value)

(PHP 5 &gt;= 5.0.0)<br/>
Offset to set



* Visibility: **public**


#### Arguments
* $name **mixed** - &lt;p&gt;
                     The offset to assign the value to.
                     &lt;/p&gt;
* $value **mixed** - &lt;p&gt;
                     The value to set.
                     &lt;/p&gt;



### offsetUnset

    void PhpOrient\Protocols\Common\ClusterMap::offsetUnset(mixed $name)

(PHP 5 &gt;= 5.0.0)<br/>
Offset to unset



* Visibility: **public**


#### Arguments
* $name **mixed** - &lt;p&gt;
                     The offset to unset.
                     &lt;/p&gt;



### count

    integer PhpOrient\Protocols\Common\ClusterMap::count()

(PHP 5 &gt;= 5.1.0)<br/>
Count elements of an object



* Visibility: **public**




### next

    void PhpOrient\Protocols\Common\ClusterMap::next()

(PHP 5 &gt;= 5.0.0)<br/>
Move forward to next element



* Visibility: **public**




### key

    mixed PhpOrient\Protocols\Common\ClusterMap::key()

(PHP 5 &gt;= 5.0.0)<br/>
Return the key of the current element



* Visibility: **public**




### valid

    boolean PhpOrient\Protocols\Common\ClusterMap::valid()

(PHP 5 &gt;= 5.0.0)<br/>
Checks if current position is valid



* Visibility: **public**




### rewind

    void PhpOrient\Protocols\Common\ClusterMap::rewind()

(PHP 5 &gt;= 5.0.0)<br/>
Rewind the Iterator to the first element



* Visibility: **public**




### current

    mixed PhpOrient\Protocols\Common\ClusterMap::current()

(PHP 5 &gt;= 5.0.0)<br/>
Return the current element



* Visibility: **public**




### fromConfig

    static PhpOrient\Protocols\Common\ClusterMap::fromConfig(array $options)

Return a new class instance configured from the given options.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $options **array** - &lt;p&gt;The options for the newly created class instance.&lt;/p&gt;


