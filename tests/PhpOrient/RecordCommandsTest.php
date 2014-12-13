<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 07/12/14
 * Time: 15.02
 *
 */

namespace PhpOrient;

use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;
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

        $res = $this->client->execute( 'recordLoad', [
            'rid' => new ID( "#9:5" ),
            'fetchPlan' => '*:2'

            , '_callback' => function ( Record $arg ){
               $this->assertNotEmpty( $arg->getOData() );

            }

        ] );

        $this->assertNotEmpty( $res );

    }

} 