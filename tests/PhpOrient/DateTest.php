<?php
/**
 * User: gremorian
 * Date: 25/10/15
 * Time: 20.02
 *
 */

namespace PhpOrient;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Common\Constants;


use PhpOrient\Abstracts\TestCase;

class DateTest extends TestCase {

    protected $db_name = 'test_date';

    public function testDoubleDeserialization(){

        $config = self::getConfig( 'connect' );

        $db_name = 'test_date';
        $client = new PhpOrient();
        $client->configure( $config );
        $client->connect( 'root', 'root' );

        try {
            $client->dbDrop( $db_name, Constants::STORAGE_TYPE_MEMORY );
        } catch ( \Exception $e ) {
//            echo $e->getMessage();
            $client->getTransport()->debug( $e->getMessage() );
        }

        $client->dbCreate( $db_name,
            Constants::STORAGE_TYPE_MEMORY,
            Constants::DATABASE_TYPE_GRAPH
        );

        $client->dbOpen( $db_name, 'admin', 'admin' );
        
        $client->command( "create class Test extends V" );
        $client->command( "create property Test.myDate date" );
        $client->command( "insert into Test set myDate='2015-01-01'" );

        $start = microtime( true );
        $result = $client->query( "SELECT FROM Test WHERE myDate <= '2015-10-17'" );
        $end = microtime( true );

        $this->assertTrue( ( $end - $start ) < 1 /* one second is too much */ );
        $this->assertNotEmpty( $result );

    }


}
