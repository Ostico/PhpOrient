<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 07/12/14
 * Time: 15.02
 *
 */

namespace PhpOrient;

use PhpOrient\Abstracts\TestCase;

use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Binary\OrientSocket;
use PhpOrient\Protocols\Common\Constants;

class PerformanceTest extends TestCase {

    protected $db_name = 'emptiness_from_which_i_fed';


    public function testDateDeserialization() {

        $this->markTestSkipped('Nothing to do');

        $client = new PhpOrient( 'localhost', 2424 );
        $client->dbOpen( "GratefulDeadConcerts", 'admin', 'admin' );
        $result = $client->query('select expand( out("sung_by") ) from #9:1');

//        print_r("\n" . 'Done, ' . count($result) . " records read. " . round( OrientSocket::$total / 1024, 3 ) . " KB" );

//        var_export($result);

    }

    public function testVersionProperty() {

        $this->markTestSkipped('Nothing to do');

        $recOrig = [ 'name' => 'foo', 'version' => '1.0.0' ];
        $rec = new Record();
        $rec->setOData( $recOrig );
        $rec->setOClass( 'Package' );
        $rec->setRid( new ID(9) );
        $result = $this->client->recordCreate( $rec );

        var_export( $result );

        $result = $this->client->query('select from V');

        var_export( $result );
    }

    public function testRestrictedProperties() {

        $this->markTestSkipped('Nothing to do');
        $client = new PhpOrient( 'localhost', 2424 );
        $client->dbOpen( "GratefulDeadConcerts", 'admin', 'admin' );
        $result = $client->query('SELECT @rid, @class, @version FROM #11:0');
//        $result = $client->query('select from followed_by limit 1');
        usleep(1000);
    }


}