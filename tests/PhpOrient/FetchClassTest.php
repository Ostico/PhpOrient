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
            'database' => static::$DATABASE
        ] );

        $this->skipTestByOrientDBVersion( [ "2.2.20", "2.2.19", "2.2.9" ] );

        $rid = $this->client->command( "select min(@rid) from V;" );

        $this->client->setFetchClass( 'PhpOrient\TestClassRecord' );
        $res = $this->client->execute( 'recordLoad', [ 'rid' => $rid->getOData()[ 'min' ] ] );


        $this->assertEquals( 'PhpOrient\TestClassRecord', get_class( $res[ 0 ] ) );

    }

} 