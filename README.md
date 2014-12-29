# PhpOrient

[![Build Status](https://travis-ci.org/Ostico/PhpOrient.svg?branch=master)](https://travis-ci.org/Ostico/PhpOrient)

A PHP driver good enough for OrientDB that uses binary protocol.

> **status: Beta**
> Please [report any bugs](https://github.com/Ostico/PhpOrient/issues) you find so that we can improve the library for everyone.

#### Requires
- PHP Version >= 5.4
- Orientdb version 1.7.4 or later.

##### PhpOrient works even on 32bit and 64bit platforms.

###### Warning, if you use a 32bit platform, you must use one of these libraries into YOUR application to avoid the loss of significant digits with Java long integers. Furthermore, these php modules should be loaded to achieve better driver performances on these systems.
- [BCMath Arbitrary Precision Mathematics](http://php.net/manual/en/refs.math.php) (Faster, recommended)
- [GNU Multiple Precision](http://php.net/manual/en/book.gmp.php)

## Installation

Main public repository of OrientDB-PHP is hosted at [https://github.com/Ostico/PhpOrient.git](https://github.com/Ostico/PhpOrient.git).

To install most recent version of library, just type
    
    git clone https://github.com/Ostico/PhpOrient.git

where you want its file to be located.

You can also want to get latest stable version, so check out Downloads section. Stables are marked with tags.

If you have not already installed globally, you have to download composer. Just run this command inside your PhpOrient directory.
```bash
php -r "readfile('https://getcomposer.org/installer');" | php
```
Now get the required libraries:
```bash
php composer.phar --no-dev install
```

## Usage
PhpOrient specify autoload information, Composer generates a vendor/autoload.php file. You can simply include this file and you will get autoloading for free and declare the use of PhpOrient Client with fully qualified name.

```php
require "vendor/autoload.php";
use PhpOrient\PhpOrient;
```
A complete phpdoc reference can be found here [ApiIndex](https://github.com/Ostico/PhpOrient/blob/master/docs/ApiIndex.md)

### Client initialization
There are several ways to initialize the client

```php
$client = new PhpOrient( 'localhost', 2424 );
$client->username = 'root';
$client->password = 'root_pass';
```

```php
$client = new PhpOrient();
$client->hostname = 'localhost';
$client->port     = 2424;
$client->username = 'root';
$client->password = 'root_pass';
```

```php
$client = new PhpOrient();
$client->configure( array(
    'username' => 'root',
    'password' => 'root_pass',
    'hostname' => 'localhost',
    'port'     => 2424,
) );
```

### Connect to perform Server Management Operations
```php
$client = new PhpOrient( 'localhost', 2424 );
$client->username = 'root';
$client->password = 'root_pass';
$client->connect();
```

```php
$client = new PhpOrient( 'localhost', 2424 );
$client->connect( 'root', 'root_pass' );
```

### Database Create
```php
$new_cluster_id = $client->dbCreate( 'my_new_database',
    PhpOrient::STORAGE_TYPE_MEMORY,   # optional, default: STORAGE_TYPE_PLOCAL
    PhpOrient::DATABASE_TYPE_GRAPH    # optional, default: DATABASE_TYPE_GRAPH
);
```

### Drop a Database
```php
$client->dbDrop( $this->db_name, 
    PhpOrient::STORAGE_TYPE_MEMORY  # optional, default: STORAGE_TYPE_PLOCAL
);
```

### Check if a DB Exists
```php
$client->dbExists( 'my_database', 
    PhpOrient::DATABASE_TYPE_GRAPH   # optional, default: DATABASE_TYPE_GRAPH
);
```

### Get the the list of databases
```php
$client->dbList();
```

### Open a Database
```php
$ClusterMap = $client->dbOpen( 'GratefulDeadConcerts, 'admin', 'admin' );
```

### Get the size of a database ( needs a DB opened )
```php
$client->dbSize();
```

### Get the range of record ids for a cluster
```php
$data = $client->dataClusterDataRange( 9 );
```

### Get the number of records in one or more clusters
```php
$client->dataClusterCount( $client->getTransport()->getClusterMap()->getIdList() );
```

### Reload the Database info
This method automatically updates the client Cluster Map. 
Can be used after a Class creation or a DataCluster Add/Drop
```php
$reloaded_list = $client->dbReload();  # $reloaded_list === $client->getTransport()->getClusterMap()
```

### Create a new data Cluster
```php
$client->dataClusterAdd( 'new_cluster', 
    PhpOrient::CLUSTER_TYPE_MEMORY  # optional, default: PhpOrient::CLUSTER_TYPE_PHYSICAL
);
```

### Drop a data cluster
```php
$client->dataClusterDrop( 11 );
```

### Get the number of records in an open database
```php
$result = $client->dbCountRecords();
```

### Send a command
This should be used only to perform not idempotent commands on a database
```php
$client->command( 'create class Animal extends V' );
$client->command( "insert into Animal set name = 'rat', specie = 'rodent'" );
```

### Make a query
```php
$client->query( 'select from followed_by limit 10' );
```

### Make an Async query ( callback )
```php
$myFunction = function( Record $record) { var_dump( $record ); };
$client->queryAsync( 'select from followed_by', [ 'fetch_plan' => '*:1', '_callback' => $myFunction ] );
```

### Load a Record
```php
$record = $client->recordLoad( new ID( '#3:0' ) )[0];
$record = $client->recordLoad( new ID( 3, 0 ) )[0];
$record = $client->recordLoad( new ID( [ 'cluster' => 3, 'position' => 0 ] ) )[0];
```

### Create a Record
```php
$recordContent = [ 'accommodation' => 'houses', 'work' => 'bazar', 'holiday' => 'sea' ];
$rec = ( new Record() )->setOData( $recordContent )->setRid( new ID( 9 /* set only the cluster ID */ ) ); 
$record = $this->client->recordCreate( $rec );
```

### Update a Record
To update a Record you must have one.

If you have not a record you can build up a new one specifying a RID and the data:
```php
$_recUp = [ 'accommodation' => 'hotel', 'work' => 'office', 'holiday' => 'mountain' ];
$recUp = ( new Record() )->setOData( $_recUp )->setOClass( 'V' )->setRid( new ID( 9, 0 ) );
$updated = $client->recordUpdate( $recUp );
```

Otherwise you can work with a previous loaded/created Record
```php
/*
 Create/Load or Query for a Record
*/
$recordContent = [ 'accommodation' => 'houses', 'work' => 'bazar', 'holiday' => 'sea' ];
$rec = ( new Record() )->setOData( $recordContent )->setRid( new ID( 9 ) );
$record = $client->recordCreate( $rec );

/*
or Query for an existent one
*/
$record = $client->query( "select from V where @rid = '#9:0'" )[0];

/*
 NOW UPDATE
*/
$_recUp = [ 'accommodation' => 'bridge', 'work' => 'none', 'holiday' => 'what??' ];
$recUp = $record->setOData( $_recUp );
$updated = $client->recordUpdate( $recUp );
```

### Load a Record with node depth navigation ( callback )
```php
$myFunction = function( Record $record) { var_dump( $record ); };
$client->recordLoad( new ID( "9", "1" ), [ 'fetch_plan' => '*:2', '_callback' => $myFunction ] );
```

### Delete a Record
```php
$delete = $client->recordDelete( new ID( 11, 1 ) );
```

### Execute OrientDB SQL Batch
```php
$cmd = 'begin;' .
       'let a = create vertex set script = true;' .
       'let b = select from v limit 1;' .
       'let e = create edge from $a to $b;' .
       'commit retry 100;';

$lastRecord = $client->sqlBatch( $cmd );
```

### Transactions
```php
// create some record stuffs
$rec2Create = [ 'oClass' => 'V', 'oData' => [ 'alloggio' => 'albergo' ] ];
$rec        = Record::fromConfig( $rec2Create );
$first_rec  = $client->recordCreate( $rec );

$rec3Create = [ 'oClass' => 'V', 'oData' => [ 'alloggio' => 'house' ] ];
$rec        = Record::fromConfig( $rec3Create );
$sec_rec    = $client->recordCreate();

//get the transaction and start it
$tx = $client->getTransactionStatement();

//BEGIN THE TRANSACTION
$tx = $tx->begin();

//IN TRANSACTION
$recUp = [ 'accommodation' => 'mountain cabin' ];
$rec2 = new Record();
$rec2->setOData( $recUp );
$rec2->setOClass( 'V' );
$rec2->setRid( $first_rec->getRid() );
$rec2->setVersion( $first_rec->getVersion() );

$updateCommand = $client->recordUpdate( $rec2 );

$createCommand = $client->recordCreate(
    ( new Record() )
        ->setOData( [ 'accommodation' => 'bungalow' ] )
        ->setOClass( 'V' )
        ->setRid( new ID( 9 ) )
);

$deleteCommand = $client->recordDelete( $sec_rec->getRid() );

//Attach to the transaction statement, they will be executed in the same order
$tx->attach( $updateCommand );  // first
$tx->attach( $createCommand );  // second
$tx->attach( $deleteCommand );  // third

$result = $tx->commit();

/**
 * @var Record $record
 */
foreach ( $result as $record ){
    if( $record->getRid() == $first_rec->getRid() ){
        $this->assertEquals( $record->getOData(), [ 'accommodation' => 'mountain cabin' ] );
        $this->assertEquals( $record->getOClass(), $first_rec->getOClass() );
    } else {
        $this->assertEquals( $record->getOData(), [ 'accommodation' => 'bungalow' ] );
        $this->assertEquals( $record->getOClass(), 'V' );
    }
}

//check for deleted record
$deleted = $client->recordLoad( $sec_rec->getRid() );
$this->assertEmpty( $deleted );
```

### A GRAPH Example
```php
require "vendor/autoload.php";
use PhpOrient\PhpOrient;

$client = new PhpOrient();
$client->configure( array(
    'username' => 'admin',
    'password' => 'admin',
    'hostname' => 'localhost',
    'port'     => 2424,
) );

$client->connect();

$client->dbCreate( 'animals', PhpOrient::STORAGE_TYPE_MEMORY );
$client->dbOpen( 'animals', 'admin', 'admin' );

$client->command( 'create class Animal extends V' );
$client->command( "insert into Animal set name = 'rat', specie = 'rodent'" );
$animal = $client->query( "select * from Animal" );


$client->command( 'create class Food extends V' );
$client->command( "insert into Food set name = 'pea', color = 'green'" );

$client->command( 'create class Eat extends E' );

$client->command( "create edge Eat from ( select from Animal where name = 'rat' ) to ( select from Food where name = 'pea' )" );

$pea_eaters = $client->query( "select expand( in( Eat )) from Food where name = 'pea'" );

$animal_foods = $client->query( "select expand( out( Eat )) from Animal" );

foreach ( $animal_foods as $food ) {
    $animal = $client->query(
        "select name from ( select expand( in('Eat') ) from Food where name = 'pea' )"
    )[0];
    $this->assertEquals( 'pea', $food[ 'name' ] );
    $this->assertEquals( 'green', $food[ 'color' ] );
    $this->assertEquals( 'rat', $animal[ 'name' ] );
}
```

## Contributions

- Fork the project.
- Make your changes.
- Add tests for it. This is important so I don’t break it in a future version unintentionally.
- Send me a pull request.
- ???
- PROFIT

# License

Apache License, Version 2.0, see [LICENSE.md](./LICENSE.md).
