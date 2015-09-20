<?php
/**
 * User: Pierre-Julien MAZENOT ( pjmazenot )
 * Date: 22/08/15
 * Time: 15.56
 */

namespace PhpOrient;

// Load test model
require_once 'Model/TestClass.php';
require_once 'Model/TestClassRecord.php';

use PhpOrient\Abstracts\TestCase;
use PhpOrient\Protocols\Binary\Data\ID;

/**
 * Class FetchClassTest
 * @package PhpOrient
 */
class FetchClassTest extends TestCase {

    protected $db_name = 'test_record_commands';

    public function testSetFetchClass() {

        $this->client->setFetchClass( 'PhpOrient\TestClassRecord' );

        $this->assertEquals( 'PhpOrient\TestClassRecord', PhpOrient::getFetchClass() );

        $this->client->setFetchClass( 'PhpOrient\TestClass' );

        $this->assertNull( PhpOrient::getFetchClass() );

    }

    public function testFetchClassQuery() {

        $this->cluster_struct = $this->client->execute( 'dbOpen', [
            'database' => 'GratefulDeadConcerts'
        ] );

        $this->client->setFetchClass( 'PhpOrient\TestClassRecord' );
        $res = $this->client->execute( 'recordLoad', [ 'rid' => new ID( "#9:5" ) ] );


        $this->assertEquals( 'PhpOrient\TestClassRecord', get_class( $res[ 0 ] ) );

    }

} 