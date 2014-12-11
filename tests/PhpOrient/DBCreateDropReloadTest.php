<?php

namespace PhpOrient;

use PhpOrient\Protocols\Common\Constants;

class DBCreateDropTest extends TestCase {

    /**
     * @var Client
     */
    protected $client;

    public function setUp(){
        $this->client = $this->createClient();

        $connection = $this->client->execute( 'connect' );
        if( $this->client->execute( 'dbExists', [ 'database' => 'test_create' ] ) ){
            $this->client->execute( 'dbDrop', [
                    'database' => 'test_create',
                    'storage_type' => Constants::STORAGE_TYPE_MEMORY
            ] );
        }

    }

    public function tearDown(){

        try {
            $this->client->execute( 'dbDrop', [
                    'database' => 'test_create',
                    'storage_type' => Constants::STORAGE_TYPE_MEMORY
            ] );
        } catch( \Exception $e ){}

    }

    public function testDBCreateDrop() {

        $result     = $this->client->execute( 'dbCreate', [
                'database' => 'test_create',
                'database_type' => Constants::DATABASE_TYPE_GRAPH,
                'storage_type' => Constants::STORAGE_TYPE_MEMORY,
        ] );

        $result     = $this->client->execute( 'dbExists', [ 'database' => 'test_create' ] );
        $this->assertTrue( $result );

        $result     = $this->client->execute( 'dbOpen', [ 'database' => 'test_create' ] );

        $this->assertNotEquals( -1, $result[ 'sessionId' ] );
        $this->assertNotEmpty( $result[ 'dataClusters' ] );


        $result     = $this->client->execute( 'dbDrop', [
                'database' => 'test_create',
                'storage_type' => Constants::STORAGE_TYPE_MEMORY
        ] );

        $result = $this->client->execute( 'dbExists', [ 'database' => 'test_create' ] );
        $this->assertFalse( $result );

    }

}
