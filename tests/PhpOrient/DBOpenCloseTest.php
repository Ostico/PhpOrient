<?php

namespace PhpOrient;
use PhpOrient\Abstracts\EmptyTestCase;

class DBOpenCloseTest extends EmptyTestCase {

    public function testDBOpenAutoConnect(){

        $result = $this->client->execute( 'dbOpen', array( 'database' => static::$DATABASE ) );

        $this->assertNotEquals( -1, $result['sessionId'] );
        $this->assertNotEmpty( count( $result ) );

        $key_exists = false;
        foreach ( $result as $cluster ){
            $key_exists = $key_exists || ( $cluster['name'] == "followed_by" );
            if( $key_exists ) { $id = $cluster['id']; break; }
            $key_exists = $key_exists || ( $cluster['name'] == "locations" );
            if( $key_exists ) { $id = $cluster['id']; break; }
        }

        $this->assertTrue( $key_exists );
        $this->assertTrue( isset( $id ) );
        $this->assertNotEmpty( $id );

    }

    public function testDBOpen(){
        $result = $this->client->execute( 'dbOpen', [ 'database' => static::$DATABASE ] );

        $this->assertNotEquals( -1, $result['sessionId'] );
        $this->assertNotEmpty( count( $result ) );

        $key_exists = false;
        foreach ( $result as $cluster ){
            $key_exists = $key_exists || ( $cluster['name'] == "followed_by" );
            if( $key_exists ) { $id = $cluster['id']; break; }
            $key_exists = $key_exists || ( $cluster['name'] == "locations" );
            if( $key_exists ) { $id = $cluster['id']; break; }
        }

        $this->assertTrue( $key_exists );
        $this->assertTrue( isset( $id ) );
        $this->assertNotEmpty( $id );

    }

    public function testDBClose() {

        $result     = $this->client->execute( 'dbOpen', [ 'database' => static::$DATABASE ] );

        $this->assertNotEquals( -1, $result[ 'sessionId' ] );
        $this->assertNotEmpty( count( $result ) );

        $this->assertEquals( 0, $this->client->execute( 'dbClose' ) );

    }

}
