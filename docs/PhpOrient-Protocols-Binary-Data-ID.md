PhpOrient\Protocols\Binary\Data\ID
===============






* Class name: ID
* Namespace: PhpOrient\Protocols\Binary\Data
* This class implements: JsonSerializable




Properties
----------


### $cluster

    public integer $cluster





* Visibility: **public**


### $position

    public integer $position





* Visibility: **public**


Methods
-------


### __construct

    mixed PhpOrient\Protocols\Binary\Data\ID::__construct(integer|string|array $cluster, integer $position)

# Record ID Constructor.



* Visibility: **public**


#### Arguments
* $cluster **integer|string|array** - &lt;p&gt;The cluster id, string representation or configuration object&lt;/p&gt;
* $position **integer** - &lt;p&gt;The position in the cluster, if $cluster is an integer.&lt;/p&gt;



### jsonSerialize

    mixed PhpOrient\Protocols\Binary\Data\ID::jsonSerialize()

(PHP 5 &gt;= 5.4.0)<br/>
Specify data which should be serialized to JSON



* Visibility: **public**




### __toString

    string PhpOrient\Protocols\Binary\Data\ID::__toString()





* Visibility: **public**




### parseString

    mixed PhpOrient\Protocols\Binary\Data\ID::parseString($input)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $input **mixed**


