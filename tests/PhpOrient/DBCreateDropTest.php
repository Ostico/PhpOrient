<?php

namespace PhpOrient;
use PhpOrient\Abstracts\TestCase;
use PhpOrient\Protocols\Common\Constants;

class DBCreateDropTest extends TestCase {

    protected $db_name = 'test_create_drop';

    public function testDBCreateDrop() {

        $config = self::getConfig('connect');
        $this->client->connect( $config['username'], $config['password'] );

        $result     = $this->client->execute( 'dbExists', [ 'database' => $this->db_name ] );
        $this->assertTrue( $result );

        $result     = $this->client->execute( 'dbOpen', [ 'database' => $this->db_name ] );

        $this->assertNotEquals( -1, $result[ 'sessionId' ] );
        $this->assertNotEmpty( $result );

        $this->client->connect( $config['username'], $config['password'] );
        $result     = $this->client->execute( 'dbDrop', [
                'database' => $this->db_name,
                'storage_type' => Constants::STORAGE_TYPE_MEMORY
        ] );

        $result = $this->client->execute( 'dbExists', [ 'database' => $this->db_name ] );
        $this->assertFalse( $result );

    }

}
