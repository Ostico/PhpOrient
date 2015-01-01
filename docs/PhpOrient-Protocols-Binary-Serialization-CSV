PhpOrient\Protocols\Binary\Serialization\CSV
===============






* Class name: CSV
* Namespace: PhpOrient\Protocols\Binary\Serialization







Methods
-------


### unserialize
```php
    array|null PhpOrient\Protocols\Binary\Serialization\CSV::unserialize(string $input)
```
##### Deserialize a record.



* Visibility: **public**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to un-serialize.</p>



### eatFirstKey
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatFirstKey(string $input)
```
##### Consume the first field key, which could be a class name.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### eatKey
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatKey(string $input)
```
##### Consume a field key, which may or may not be quoted.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### eatValue
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatValue(string $input)
```
##### Consume a field value.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### eatString
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatString(string $input)
```
##### Consume a string.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### eatNumber
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatNumber(string $input)
```
##### Consume a number.

If the number has a suffix, consume it also and instantiate the right type, e.g. for dates

* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### eatRID
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatRID(string $input)
```
##### Consume a Record ID.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### eatArray
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatArray(string $input)
```
##### Consume an array of values.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### eatSet
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatSet(string $input)
```
##### Consume a set of values.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### eatMap
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatMap(string $input)
```
##### Consume a map of keys to values.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### eatRecord
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatRecord(string $input)
```
##### Consume an embedded record.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to unserialize.</p>



### eatBag
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatBag(string $input)
```
##### Consume a record id bag.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### eatBinary
```php
    array PhpOrient\Protocols\Binary\Serialization\CSV::eatBinary(string $input)
```
##### Consume a binary field.



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $input **string** <p>The input to consume</p>



### serialize
```php
    string PhpOrient\Protocols\Binary\Serialization\CSV::serialize(mixed $value, boolean $embedded)
```
##### Serialize a value.



* Visibility: **public**
* This method is **static**.


##### Arguments
* $value **mixed** <p>The value to serialize.</p>
* $embedded **boolean** <p>Whether this is a value embedded in another.</p>



### serializeDocument
```php
    mixed PhpOrient\Protocols\Binary\Serialization\CSV::serializeDocument(\PhpOrient\Protocols\Binary\Abstracts\SerializableInterface $document, $embedded)
```
##### 



* Visibility: **protected**
* This method is **static**.


##### Arguments
* $document **[PhpOrient\Protocols\Binary\Abstracts\SerializableInterface](PhpOrient-Protocols-Binary-Abstracts-SerializableInterface)**
* $embedded **mixed**



### serializeArray
```php
    string PhpOrient\Protocols\Binary\Serialization\CSV::serializeArray(array $array)
```
##### Serialize an array of values.

If the array is associative a `map` will be returned, otherwise a plain array.

* Visibility: **protected**
* This method is **static**.


##### Arguments
* $array **array** <p>the array to serialize</p>


