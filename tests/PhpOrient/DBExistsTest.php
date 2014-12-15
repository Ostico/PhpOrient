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
        $connection = $this->client->execute( 'connect' );
        $result = $this->client->execute( 'dbExists', [ 'database' => 'GratefulDeadConcerts' ] );
        $this->assertTrue( $result );
    }

    public function testDBNOTExists(){
        $connection = $this->client->execute( 'connect' );
        $result = $this->client->execute( 'dbExists', [ 'database' => 'asjbgfakjlghajlfg' ] );
        $this->assertFalse( $result );
    }

} 