<?php

namespace PhpOrient;
use PhpOrient\Abstracts\TestCase;
use PhpOrient\Protocols\Common\Constants;

class DBCreateDropTest extends TestCase {

    protected $db_name = 'test_create_drop';

    public function testDBCreateDrop() {

//        if( $this->client->getTransport()->getProtocolVersion() < 26 ){
            $this->markTestSkipped( 'Record Create/Update Unpredictable Behaviour' );
//        }

        $this->client->connect( self::getConfig('connect') );

        $result     = $this->client->execute( 'dbExists', [ 'database' => $this->db_name ] );
        $this->assertTrue( $result );

        $result     = $this->client->execute( 'dbOpen', [ 'database' => $this->db_name ] );

        $this->assertNotEquals( -1, $result[ 'sessionId' ] );
        $this->assertNotEmpty( $result );

        $this->client->connect( self::getConfig('connect') );
        $result     = $this->client->execute( 'dbDrop', [
                'database' => $this->db_name,
                'storage_type' => Constants::STORAGE_TYPE_MEMORY
        ] );

        $result = $this->client->execute( 'dbExists', [ 'database' => $this->db_name ] );
        $this->assertFalse( $result );

    }

}
