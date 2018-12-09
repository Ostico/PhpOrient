<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 06/12/14
 * Time: 19.14
 *
 */

namespace PhpOrient;
use PhpOrient\Abstracts\EmptyTestCase;
class DBExistsTest extends EmptyTestCase {

    public function testDBExists(){

        $config = self::getConfig('connect');

        $connection = $this->client->execute( 'connect', $config );
        $result = $this->client->execute( 'dbExists', [ 'database' => static::$DATABASE ] );
        $this->assertTrue( $result );
    }

    public function testDBNOTExists(){
        $config = self::getConfig('connect');

        $connection = $this->client->execute( 'connect', $config );
        $result = $this->client->execute( 'dbExists', [ 'database' => 'asjbgfakjlghajlfg' ] );
        $this->assertFalse( $result );
    }

} 