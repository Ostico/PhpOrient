<?php

namespace PhpOrient;

use PhpOrient\Protocols\Common\Constants;
use PhpOrient\Abstracts\EmptyTestCase;

class DBMinorCommandsTest extends EmptyTestCase {

    protected $db_name = 'test_freeze';

    public function testFreezeRelease() {

        $this->client->execute( 'connect', self::getConfig('connect') );

        try {
            $this->client->execute( 'dbDrop', [
                'database'     => $this->db_name,
                'storage_type' => Constants::STORAGE_TYPE_MEMORY
            ] );
        } catch ( \Exception $e ) {
            $this->client->getTransport()->debug( $e->getMessage() );
        }

        $this->client->execute( 'dbCreate', [
            'database'      => $this->db_name,
            'database_type' => Constants::DATABASE_TYPE_GRAPH,
            'storage_type'  => Constants::STORAGE_TYPE_MEMORY,
        ] );

        $result     = $this->client->execute( 'dbExists', [ 'database' => $this->db_name ] );
        $this->assertTrue( $result );

        $result     = $this->client->execute( 'dbOpen', [ 'database' => $this->db_name ] );
        $this->assertNotEmpty( $this->client->getTransport()->getClusterMap() );

        $this->client->execute( 'connect', self::getConfig('connect') );
        $result     = $this->client->execute( 'dbFreeze', [ 'database' => $this->db_name ] );
        $this->assertNotEquals( -1, $result[ 'sessionId' ] );

        $result     = $this->client->execute( 'dbRelease', [ 'database' => $this->db_name ] );
        $this->assertNotEquals( -1, $result[ 'sessionId' ] );


        $result     = $this->client->execute( 'dbDrop', [
                'database' => $this->db_name,
                'storage_type' => Constants::STORAGE_TYPE_MEMORY
        ] );

        $result = $this->client->execute( 'dbExists', [ 'database' => $this->db_name ] );
        $this->assertFalse( $result );

    }

    public function testDBSize(){
        $result     = $this->client->execute( 'dbOpen', [ 'database' => 'GratefulDeadConcerts' ] );
        $this->assertNotEmpty( count($result) );
        $result = $this->client->execute( 'dbSize' );
        $this->assertNotEmpty( $result );

    }

    public function testDBSizeWithoutDB(){
        $this->setExpectedException( '\PhpOrient\Exceptions\PhpOrientException',
                'Can not perform DbSize operation on a Database without open it.' );
        $result = $this->client->execute( 'dbSize' );

    }

    public function testRecordCount(){
        $result     = $this->client->execute( 'dbOpen', [ 'database' => 'GratefulDeadConcerts' ] );
        $this->assertNotEmpty( count($result) );
        $result = $this->client->execute( 'dbCountRecords' );
        $this->assertNotEmpty( $result );

    }

    public function testRecordCountWithoudDB(){
        $this->setExpectedException( '\PhpOrient\Exceptions\PhpOrientException',
                'Can not perform DbCountRecords operation on a Database without open it.' );
        $result = $this->client->execute( 'dbCountRecords' );
        $this->assertNotEmpty( $result );

    }

}
