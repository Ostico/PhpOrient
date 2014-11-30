# PHP-OrientDB

[![Build Status](https://travis-ci.org/codemix/php-orientdb.svg?branch=master)](https://travis-ci.org/codemix/php-orientdb)

A fast PHP driver for the OrientDB binary protocol.


> **status: alpha**
> This is work in progress, alpha quality software.
> Please [report any bugs](https://github.com/codemix/php-orientdb/issues) you find so that we can improve the library for everyone.

## Usage

### Configuring the client

```php
$client = new OrientDB\Client([
  'hostname' => 'localhost',
  'port' => 2424,
  'username' => 'root',
  'password' => 'yourpassword'
]);
```

### List the databases on the server.

```php
foreach($client->getDatabases() as $name => $db) {
  echo $name, "<br>";
}
```

### Use a particular database

```php
$db = $client->getDatabases()->mydatabase;
```

### Use a particular database, with custom credentials

```php
$db = $client->getDatabases()->mydatabase;
$db->username = 'me';
$db->password = 'mypassword';
```

### Create a new database

```php
$db = $client->getDatabases()->create('mydatabase', 'plocal');
```

### Drop an existing database

```php
$client->getDatabases()->drop('mydatabase', 'plocal');
```

### Query builder: Insert record

```php
$record = $db->insert(['name' => 'Me'])->into('MyClass')->one();
```

### Query builder: Update record

```php
$db->update('MyClass')->set(['name' => 'Charles'])->where(['name' => 'Me'])->one();
```

### Query builder: Delete record

```php
$db->delete('MyClass')->where(['name' => 'Charles'])->one();
```

### Query builder: Select records

```php
$results = $db->select('name')->from('OUser')->groupBy('status')->all();
```
### Query builder: Select records with fetch plan.

```php
$results = $db->select()->from('OUser')->where(['status' => 'ACTIVE'])->fetch(['roles' => 2])->all();
```

### Query builder: Select expression

```php
$totalUsers = $db->select('count(*)')->from('OUser')->scalar();
```

### Query builder: Traverse records

```php
$rows = $db->traverse()->from('OUser');
```


### List all the clusters in the database.

```php
foreach($db->clusters as $name => $cluster) {
  echo 'Cluster '.$name.' has '.$cluster->count()." records.\n";
}
```

### List all the classes in the database.

```php
foreach($db->classes as $name => $class) {
  echo $name."\n";
}
```


# License

MIT, see [LICENSE.md](./LICENSE.md).
