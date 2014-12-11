<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 07/12/14
 * Time: 15.02
 *
 */

namespace PhpOrient;

use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Common\Constants;

class RecordCommandsTest extends TestCase {

    /**
     * @var Client
     */
    protected $client;

    protected $db_name = 'GratefulDeadConcerts';

    protected $cluster_struct;

    public function setUp() {
        $this->client = $this->createClient();

        $this->client->execute( 'connect' );

        $this->cluster_struct = $this->client->execute( 'dbOpen', [
                'database' => $this->db_name
        ] );

    }

    public function tearDown() {

        try {

        } catch ( \Exception $e ) {
        }

    }

    public function testRecordLoad() {

        function _print( $arg ){
        }

        $res = $this->client->execute( 'recordLoad', [
            'rid' => new ID( "#9:5" ),
            'fetchPlan' => '*:0',
            '_callback' => '_print'
        ] );

        foreach( $res['oData']['in_followed_by'] as $k => $v ){
            echo $k . " => " . var_export( $v, true ) ."\n";
        }

//        $res = $this->client->execute( 'recordLoad', [
//            'rid' => new ID( "#11:5" )
//        ] );
//        var_export( $res );

    }

} 