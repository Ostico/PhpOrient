<?php

namespace PhpOrient;

class DBOpenCloseTest extends TestCase {

    /**
     * @var Client
     */
    protected $client;

    public function setUp(){
        $this->client = $this->createClient();
    }

    public function testDBOpenAutoConnect(){

        $result = $this->client->execute( 'dbOpen', array( 'database' => 'GratefulDeadConcerts' ) );

        $this->assertNotEquals( -1, $result['sessionId'] );
        $this->assertNotEmpty( $result['dataClusters'] );

        $key_exists = false;
        foreach ( $result['dataClusters'] as $cluster ){
            $key_exists = $key_exists || ( $cluster['name'] == "followed_by" );
            if( $key_exists ) { $id = $cluster['id']; break; }
        }

        $this->assertTrue( $key_exists );
        $this->assertTrue( isset( $id ) );
        $this->assertEquals( 11, $id );

    }

    public function testDBOpen(){
        $connection = $this->client->execute( 'connect' );
        $result = $this->client->execute( 'dbOpen', [ 'database' => 'GratefulDeadConcerts' ] );

        $this->assertNotEquals( -1, $result['sessionId'] );
        $this->assertNotEmpty( $result['dataClusters'] );

        $key_exists = false;
        foreach ( $result['dataClusters'] as $cluster ){
            $key_exists = $key_exists || ( $cluster['name'] == "followed_by" );
            if( $key_exists ) { $id = $cluster['id']; break; }
        }

        $this->assertTrue( $key_exists );
        $this->assertTrue( isset( $id ) );
        $this->assertEquals( 11, $id );

    }

    public function testDBClose() {

        $connection = $this->client->execute( 'connect' );
        $result     = $this->client->execute( 'dbOpen', [ 'database' => 'GratefulDeadConcerts' ] );

        $this->assertNotEquals( -1, $result[ 'sessionId' ] );
        $this->assertNotEmpty( $result[ 'dataClusters' ] );

        $this->assertEquals( 0, $this->client->execute( 'dbClose' ) );

    }

}
