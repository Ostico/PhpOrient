<?php

namespace PhpOrient;

use PhpOrient\Protocols\Common\Constants;

class DBFreezeTest extends TestCase {

    /**
     * @var Client
     */
    protected $client;

    public function setUp(){
        $this->client = $this->createClient();
    }

    public function testFreezeRelease() {

        $connection = $this->client->execute( 'connect' );

        if( $this->client->execute( 'dbExists', [ 'database' => 'test_freeze' ] ) ){
            $this->client->execute( 'dbDrop', [
                    'database' => 'test_freeze',
                    'storage_type' => Constants::STORAGE_TYPE_MEMORY
            ] );
        }

        $result     = $this->client->execute( 'dbCreate', [
                'database' => 'test_freeze',
                'database_type' => Constants::DATABASE_TYPE_GRAPH,
                'storage_type' => Constants::STORAGE_TYPE_MEMORY,
        ] );

        $result     = $this->client->execute( 'dbExists', [ 'database' => 'test_freeze' ] );
        $this->assertTrue( $result );

        $result     = $this->client->execute( 'dbOpen', [ 'database' => 'test_freeze' ] );
        $this->assertNotEmpty( $result[ 'dataClusters' ] );

        $result     = $this->client->execute( 'dbFreeze', [ 'database' => 'test_freeze' ] );
        $this->assertNotEquals( -1, $result[ 'sessionId' ] );

        $result     = $this->client->execute( 'dbRelease', [ 'database' => 'test_freeze' ] );
        $this->assertNotEquals( -1, $result[ 'sessionId' ] );

        $result     = $this->client->execute( 'dbDrop', [
                'database' => 'test_freeze',
                'storage_type' => Constants::STORAGE_TYPE_MEMORY
        ] );

        $result = $this->client->execute( 'dbExists', [ 'database' => 'test_freeze' ] );
        $this->assertFalse( $result );

    }

    public function testDBSize(){
        $connection = $this->client->execute( 'connect' );
        $result     = $this->client->execute( 'dbOpen', [ 'database' => 'GratefulDeadConcerts' ] );
        $this->assertNotEmpty( $result[ 'dataClusters' ] );
        $result = $this->client->execute( 'dbSize' );
        $this->assertNotEmpty( $result );

    }

    public function testDBSizeWithoutDB(){
        $connection = $this->client->execute( 'connect' );
        $this->setExpectedException( '\PhpOrient\Exceptions\PhpOrientException',
                'Can not perform DbSize operation on a Database without open it.' );
        $result = $this->client->execute( 'dbSize' );

    }

    public function testRecordCount(){
        $connection = $this->client->execute( 'connect' );
        $result     = $this->client->execute( 'dbOpen', [ 'database' => 'GratefulDeadConcerts' ] );
        $this->assertNotEmpty( $result[ 'dataClusters' ] );
        $result = $this->client->execute( 'dbCountRecords' );
        $this->assertNotEmpty( $result );

    }

    public function testRecordCountWithoudDB(){
        $connection = $this->client->execute( 'connect' );
        $this->setExpectedException( '\PhpOrient\Exceptions\PhpOrientException',
                'Can not perform DbCountRecords operation on a Database without open it.' );
        $result = $this->client->execute( 'dbCountRecords' );
        $this->assertNotEmpty( $result );

    }

}
