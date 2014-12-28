PhpOrient\Protocols\Binary\Serialization\CSV
===============






* Class name: CSV
* Namespace: PhpOrient\Protocols\Binary\Serialization







Methods
-------


### unserialize

    array|null PhpOrient\Protocols\Binary\Serialization\CSV::unserialize(string $input)

Deserialize a record.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to un-serialize.&lt;/p&gt;



### eatFirstKey

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatFirstKey(string $input)

Consume the first field key, which could be a class name.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### eatKey

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatKey(string $input)

Consume a field key, which may or may not be quoted.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### eatValue

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatValue(string $input)

Consume a field value.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### eatString

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatString(string $input)

Consume a string.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### eatNumber

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatNumber(string $input)

Consume a number.

If the number has a suffix, consume it also and instantiate the right type, e.g. for dates

* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### eatRID

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatRID(string $input)

Consume a Record ID.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### eatArray

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatArray(string $input)

Consume an array of values.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### eatSet

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatSet(string $input)

Consume a set of values.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### eatMap

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatMap(string $input)

Consume a map of keys to values.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### eatRecord

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatRecord(string $input)

Consume an embedded record.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to unserialize.&lt;/p&gt;



### eatBag

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatBag(string $input)

Consume a record id bag.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### eatBinary

    array PhpOrient\Protocols\Binary\Serialization\CSV::eatBinary(string $input)

Consume a binary field.



* Visibility: **protected**
* This method is **static**.


#### Arguments
* $input **string** - &lt;p&gt;The input to consume&lt;/p&gt;



### serialize

    string PhpOrient\Protocols\Binary\Serialization\CSV::serialize(mixed $value, boolean $embedded)

Serialize a value.



* Visibility: **public**
* This method is **static**.


#### Arguments
* $value **mixed** - &lt;p&gt;The value to serialize.&lt;/p&gt;
* $embedded **boolean** - &lt;p&gt;Whether this is a value embedded in another.&lt;/p&gt;



### serializeDocument

    mixed PhpOrient\Protocols\Binary\Serialization\CSV::serializeDocument(\PhpOrient\Protocols\Binary\Abstracts\SerializableInterface $document, $embedded)





* Visibility: **protected**
* This method is **static**.


#### Arguments
* $document **[PhpOrient\Protocols\Binary\Abstracts\SerializableInterface](PhpOrient-Protocols-Binary-Abstracts-SerializableInterface.md)**
* $embedded **mixed**



### serializeArray

    string PhpOrient\Protocols\Binary\Serialization\CSV::serializeArray(array $array)

Serialize an array of values.

If the array is associative a `map` will be returned, otherwise a plain array.

* Visibility: **protected**
* This method is **static**.


#### Arguments
* $array **array** - &lt;p&gt;the array to serialize&lt;/p&gt;


