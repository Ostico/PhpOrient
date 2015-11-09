<?php
/**
 * User: gremorian
 * Date: 25/10/15
 * Time: 20.02
 *
 */

namespace PhpOrient;
use PhpOrient\Protocols\Binary\Data\Record,
    PhpOrient\Protocols\Binary\Data\ID;


use PhpOrient\Abstracts\TestCase;

class DoubleTest extends TestCase {

    protected $db_name = 'animals';

    public function testDoubleDeserialization(){

        $this->client->command( "create class Test extends V" );
        $this->client->command( "create property Test.stringList embeddedlist string" );
        $this->client->command( "create property Test.stringMap embeddedmap string" );
        $this->client->command( "create property Test.stringValue string" );
        $this->client->command( "create property Test.doubleList embeddedlist double" );
        $this->client->command( "create property Test.doubleMap embeddedmap double" );
        $this->client->command( "create property Test.doubleValue double" );
        $this->client->command( "create property Test.longList embeddedlist long" );
        $this->client->command( "create property Test.longMap embeddedmap long" );
        $this->client->command( "create property Test.longValue long" );

//create record
        $odata= [
            "stringMap" => [
                "attr1" => (double) 1231231232,
                "attr2" => (int) 1231231233,
                "attr3" => "hi there",
            ],

            "doubleValue" => (double) 123123123,

            "stringList" => [
                "aaaa", "bbbb"
            ],

            "doubleMap" => [
                "attr1" => 1231231232,
                "attr2" => (double) 1231231232,
            ],

            "longList" => [
                123123123, (double) 123123123
            ],

            "stringValue" => 123123123,

            "doubleList" => [
                123123123, (double) 123123123
            ],

            "longValue" => 123123123,

            "longMap" => [
                "attr1" => 1231231232,
                "attr2" => (double) 1231231232,
                "attr3" => "1231231232",
            ],

        ];

        $rec = ( new Record() )->setOData( $odata )->setOClass("Test")->setRid( new ID( 11 ) );
        $rec = $this->client->recordCreate( $rec );

//re-load record
        /**
         * @var $record Record
         */
        $record = $this->client->recordLoad( $rec->getRid() )[0];
        $this->assertEquals( 9, count( $record->getOData() ) );

//        $this->assertEquals( $odata, $record->getOData() );

//output
//        var_export( $record->getOData() );

    }

}
