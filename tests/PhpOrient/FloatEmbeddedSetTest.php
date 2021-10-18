<?php
/**
 * Created by PhpStorm.
 * User: Domenico Lupinetti <ostico@gmail.com>
 * Date: 26/11/16
 * Time: 22:12
 */

namespace PhpOrient;

use PhpOrient\Abstracts\TestCase;
use PhpOrient\PhpOrient;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;


class FloatEmbeddedSetTest extends TestCase {

    protected $db_name = 'test_emb_float';

    public function testOrientEmbeddedFloatDeserialization(){

        $this->skipTestByOrientDBVersion( [
                '2.1.25',
                '2.1.24',
                '2.0.18',
        ] );

        $className = "Test";

        $this->client->command( "create class SubTest" );
        $this->client->command( "create property SubTest.id string" );
        $this->client->command( "create property SubTest.value1 double" );
        $this->client->command( "alter property SubTest.value1 default 0.0" );
        $this->client->command( "create property SubTest.value1Name string" );
        $this->client->command( "create property SubTest.value2 double" );
        $this->client->command( "alter property SubTest.value2 default 0.0" );
        $this->client->command( "create class $className extends V" );
        $this->client->command( "create property $className.attr1 embeddedlist SubTest" );

        $this->cluster_struct = $this->client->dbReload();

        //create record
        $subOData = [
                "id"         => "123456-78980",
                "value1Name" => $className,
                "value1"     => 2000,
                "value2"     => 33333,
        ];

        $attr1Item = ( new Record() )->setOData( $subOData )->setOClass( "SubTest" );

        $odata = [
                "attr1" => [
                        $attr1Item
                ],
        ];

        $rec = ( new Record() )->setOClass( $className )->setOData( $odata )->setRid(
                new ID( $this->cluster_struct->getClusterID( $className ) /* YOUR CLUSTER ID FOR TEST CLASS */ )
        );

        $rec = $this->client->recordCreate( $rec );

        //re-load record
        $recLoaded = $this->client->recordLoad( $rec->getRid() )[ 0 ];

        $this->assertEquals( $rec, $recLoaded );
        
    }
    
}